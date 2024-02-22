<style>
svg {
    max-width: 100%;
    height: auto;
}
.pie-slice {
    stroke: transparent;
    stroke-width: 0.125rem;
    transition: all 100ms;
}
.pie-slice:hover {
    stroke: red;
    /* translate: -2px -2px; */
}
.label {
    text-anchor: middle;
    font-size: 0.75rem;
}
</style>

<template>
    <div class="chart" ref="chartContainer"></div>
</template>

<script setup>
import { onMounted, watch, ref } from "vue";
import * as d3 from "d3";

const chartContainer = ref(null);
const props = defineProps({
    data: {
        type: Array,
        required: true,
    },
});

watch(
    () => props.data,
    (newVal, oldVal) => {
        if (oldVal.length) {
            updateChart();
        } else {
            renderChart();
        }
    }
);

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

const width = 275;
const height = Math.min(width, 275);
const radius = Math.min(width, height) / 2;

const arc = d3
    .arc()
    .innerRadius(radius * 0.4)
    .outerRadius(radius - 1);

const pie = d3
    .pie()
    .padAngle(1 / radius)
    .sort(null)
    .value((d) => d.value);

const getColorScale = () => {
    return d3
        .scaleOrdinal()
        .domain(taxa_levels)
        .range(
            d3
                .quantize(
                    (t) => d3.interpolateSpectral(t * 0.8 + 0.1),
                    taxa_levels.length
                )
                .reverse()
        );
};

const initializeSVG = () => {
    let svg = d3.select(chartContainer.value.querySelector("svg"));

    if (!svg.node()) {
        svg = d3
            .select(chartContainer.value)
            .append("svg")
            .attr("width", width)
            .attr("height", height)
            .attr("viewBox", [-width / 2, -height / 2, width, height])
            .append("g");
    }
    return svg;
};

const renderLabels = (labels, pieData) => {
    labels
        .data(pieData)
        .enter()
        .append("text")
        .classed("label", true)
        .attr("dy", ".35em")
        .attr("transform", (d) => `translate(${arc.centroid(d)})`)
        .text((d) => `${d.data.name}: ${d.data.value.toLocaleString()}`);
};

const positionLabels = (labels) => {
    labels.each(function (d, i) {
        const thisLabel = d3.select(this);
        const centroid = arc.centroid(d);
        const midAngle = (d.startAngle + d.endAngle) / 2;
        const x = centroid[0] * 1;
        const y = centroid[1] * 1;

        let rotation = midAngle;
        rotation = rotation * (180 / Math.PI);
        if ((midAngle < Math.PI && x > 0) || (midAngle > Math.PI && x < 0)) {
            thisLabel.attr(
                "transform",
                `translate(${x}, ${y}) rotate(-90) rotate(${rotation})`
            );
        } else {
            const labelX = x + Math.cos(midAngle) * radius * 0.8;
            const labelY = y + Math.sin(midAngle) * radius * 0.8;
            thisLabel.attr("transform", `translate(${labelX}, ${labelY})`);
        }
    });
};

const renderChart = () => {
    console.log("render");
    const color = getColorScale();
    const svg = initializeSVG();
    const pieData = pie(props.data);

    const path = svg
        .selectAll("path")
        .data(pieData)
        .enter()
        .append("path")
        .attr("fill", (d) => color(d.data.name))
        .classed("pie-slice", true)
        .attr("d", arc);

    path.append("title").text(
        (d) => `${d.data.name}: ${d.data.value.toLocaleString()}`
    );

    const labels = svg.selectAll(".label");
    renderLabels(labels, pieData);
    positionLabels(labels);
};

const updateChart = () => {
    console.log("update");
    let svg = d3.select(chartContainer.value.querySelector("svg"));

    const pieData = pie(props.data);
    const path = svg.selectAll("path").data(pieData);

    path.transition()
        .duration(50) // Animation duration
        .attrTween("d", function (d) {
            const interpolate = d3.interpolate(this._current, d);
            this._current = d; // Store the updated data for interpolation
            return function (t) {
                return arc(interpolate(t));
            };
        });

    path.enter()
        .append("path")
        .attr("fill", (d) => color(d.data.name))
        .classed("pie-slice", true)
        .attr("d", arc);

    path.append("title").text(
        (d) => `${d.data.name}: ${d.data.value.toLocaleString()}`
    );
    path.exit().remove();

    const labels = svg.selectAll(".label");
    renderLabels(labels, pieData);
    positionLabels(labels);
};
</script>
