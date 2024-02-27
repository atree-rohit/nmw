<style>
.pie-chart-container svg {
    max-width: 100%;
    height: auto;
}
.pie-chart-container .pie-slice {
    stroke: transparent;
    stroke-width: 0.125rem;
    transition: all 100ms;
}
.pie-chart-container .pie-slice:hover {
    stroke: red;
}
.pie-chart-container .pie-slice.selected {
    stroke: red;
    stroke-width: 0.25rem;
}

.pie-chart-container .label {
    text-anchor: middle;
    font-size: 1.15rem;
}
foreignObject.center-text {
    padding: 0.25rem;
    border-radius: 33%;
}
foreignObject.center-text body {
    text-transform: capitalize;
    font-size: 1.5rem;
    text-align: center;
    /* border: 1px solid saddlebrown; */
}
foreignObject.center-text.none-selected body {
    fill: red;
    opacity: 0.8;
}
.pie-chart-container .controls-container {
    top: 80px;
}
.pie-chart-container:hover .controls-container {
    opacity: 1;
}
</style>

<template>
    <div class="pie-chart-container py-3">
        <span class="controls-container">
            <button
                type="button"
                class="btn btn-sm mx-1"
                v-for="(mode, m_id) in data_modes"
                :key="m_id"
                :class="
                    selected_data_mode == m_id
                        ? 'btn-success'
                        : 'btn-outline-danger'
                "
                v-text="mode"
                @click="selected_data_mode = m_id"
            />
        </span>
        <div class="chart" ref="chartContainer"></div>
    </div>
</template>

<script setup lang="js">
import { onMounted, watch, ref, computed } from "vue";
import * as d3 from "d3";
import { useStore } from "vuex";

const props = defineProps({
    year: {
        type: Number,
        required: true,
    },
});

const store = useStore();
const nmw_data = computed(() => store.state.nmw_data);
const id_levels_data = computed(()=> nmw_data.value[props.year]?.count_id_levels)
const data_modes = ["Observations", "Taxa"]
const selected_data_mode = ref(0)

const data = computed(() => {
    let op = []
    if(!id_levels_data.value){
        return op
    }
    const taxa_levels = ["order","superfamily","family","subfamily","tribe","subtribe","genus","species","subspecies","complex"];
    taxa_levels.forEach((t) => {
        if(id_levels_data.value[t]){
            op.push({
                name: t,
                value: id_levels_data.value[t][data_modes[selected_data_mode.value].toLowerCase()]
            })

        }
    })
    return op

})
const chartContainer = ref(null);
const taxa_levels = [
    "order",
    "superfamily",
    "family",
    "subfamily",
    "tribe",
    "subtribe",
    "genus",
    "species",
    "subspecies",
    "complex",
];
const width = window.innerWidth / 1.5;
const height = window.innerHeight / 1.5;
const radius = Math.min(width, height) / 2;

const svg = ref(null);
const arc = d3.arc().innerRadius(radius * 0.4).outerRadius(radius - 1);
const pie = d3.pie().padAngle(1 / radius).sort(null).value((d) => d.value);
const pieData = computed(() => pie(data.value));

const color = computed(() => {
    return d3
        .scaleOrdinal().domain(taxa_levels).range(d3.quantize((t) => d3.interpolateSpectral(t * 1 + 0.01),taxa_levels.length).reverse());
});

const selectedSlice = ref(null);
const centerCircleText = computed(() => selectedSlice.value  ? getLabelString(selectedSlice.value).replace(":", ""): "Click a slice to see data")

onMounted(() => {
    renderChart();
})

watch(() => data.value, (newVal, oldVal) => {
    handleClick({}, null)
    oldVal.length ? updateChart() : renderChart();
});

const initSVG = () => {
    if(!svg.value) {
        svg.value = d3.select(chartContainer.value).append("svg").attr("width", width).attr("height", height).attr("viewBox", [-width / 2, -height / 2, width, height]).append("g");
    }
}

const getLabelString = (data) =>  `${data.name}: ${data.value.toLocaleString()}`;

const renderLabels = () => {
    svg.value.selectAll(".label").remove();
    const labels = svg.value.selectAll(".label");
    labels
        .data(pieData.value)
        .enter()
        .append("text")
        .classed("label", true)
        .attr("dy", ".35em")
        .attr("transform", (d) => {
            const [x, y] = arc.centroid(d);
            let op = `translate(${x}, ${y}) `
            const midAngle = (d.startAngle + d.endAngle) / 2;
            let rotation = midAngle * (180 / Math.PI);
            op += midAngle > Math.PI ? `rotate(90) rotate(${rotation})` : `rotate(-90) rotate(${rotation})`;
            return op

        })
        .text((d) => getLabelString(d.data))
        .on("click", handleClick);
};

const handleClick = (event, d) => {
    selectedSlice.value = selectedSlice.value?.name === d?.data?.name ? null : d?.data;

    svg.value.selectAll(".pie-slice").classed("selected", false);
    if (selectedSlice.value) {
        d3.select(event?.currentTarget).classed("selected", true);
    }
    addCenterText()
};

const drawCenterCircle = () => {
    const centerX = width / 2;
    const centerY = height / 2;
    const radius = Math.min(width, height) / 4;
    const centerCircle = svg.value.selectAll(".center-circle")
        .data([null])
        .enter()
        .append("circle")
        .classed("center-circle", true)
        .attr("x", centerX)
        .attr("y", centerY)
        .attr("r", radius / 1.3)
        .attr("fill", "none")
        .attr("stroke", "red")
        .attr("stroke-width", 2)
        .attr("stroke-dasharray", "4")
        .attr("pointer-events", "none");
    addCenterText()
    centerCircle.exit().remove();
};

const removeCenterCircle = () => {
    svg.value.selectAll(".center-circle").remove();
    svg.value.selectAll(".center-text").remove();
}

const addCenterText = () => {
    svg.value.selectAll(".center-text").remove();

    const rad = radius * 0.5

    let side = 2 * rad * Math.cos(Math.PI / 4) / 2
    let dx = rad / 2 - side / 2;


    let centerX = width / 2;
    let centerY = height / 2;

    let g = svg.value.append("foreignObject")
        .attr("width", rad)
        .attr("height", rad)
        .attr('transform', 'translate(' + [-rad /2, -rad/2] + ')')
        .attr('class', () => selectedSlice.value ? 'center-text' : 'center-text none-selected')
        .append("xhtml:body")
        .style("display", "flex")
        .style("justify-content", "center")
        .style("align-items", "center")
        .style("height", "100%")
        .text(centerCircleText.value);
}


const renderChart = () => {
    initSVG();
    const path = svg.value.selectAll("path").data(pieData.value).enter().append("path").attr("fill", (d) => color.value(d.data.name)).classed("pie-slice", true).attr("d", arc).on("click", handleClick);
    path.append("title").text((d) => getLabelString(d.data));
    renderLabels();
    drawCenterCircle();
};

const updateChart = () => {
    const path = svg.value.selectAll("path").data(pieData.value);
    path.transition().duration(50).attrTween("d", function (d) {
        const interpolate = d3.interpolate(this._current, d);
        this._current = d;
        return function (t) {
            return arc(interpolate(t));
        };
    });
    path.enter().append("path").attr("fill", (d) => color.value(d.data.name)).classed("pie-slice", true).attr("d", arc);
    path.select("title").text((d) => getLabelString(d.data));
    path.exit().remove();
    renderLabels();
    if(Object.keys(pieData.value) == 0){
        removeCenterCircle()
    }
};
</script>
