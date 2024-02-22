<template>
    <div class="chart-container" ref="chartContainer"></div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
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
        if (newVal) {
            if (!d3.select(chartContainer.value.querySelector("svg")).node()) {
                renderChart();
            } else {
                updateChart();
            }
        }
    }
);

const renderChart = () => {
    const width = 275;
    const height = 275;
    const marginTop = 30;
    const marginRight = 0;
    const marginBottom = 30;
    const marginLeft = 40;

    const x = d3
        .scaleBand()
        .domain(props.data.map((d) => d.name)) // descending frequency
        .range([marginLeft, width - marginRight])
        .padding(0.1);

    const y = d3
        .scaleLinear()
        .domain([0, d3.max(props.data, (d) => d.value)])
        .range([height - marginBottom, marginTop]);

    const svg = d3
        .create("svg")
        .attr("width", width)
        .attr("height", height)
        .attr("viewBox", [0, 0, width, height])
        .attr("style", "max-width: 100%; height: auto;");

    svg.append("g")
        .attr("fill", "steelblue")
        .selectAll()
        .data(props.data)
        .join("rect")
        .attr("x", (d) => x(d.name))
        .attr("y", (d) => y(d.value))
        .attr("height", (d) => y(0) - y(d.value))
        .attr("width", x.bandwidth())
        .append("title")
        .text((d) => `${d.value}`);

    svg.append("g")
        .classed("x-axis", true)
        .attr("transform", `translate(0,${height - marginBottom})`)
        .call(d3.axisBottom(x).tickSizeOuter(0));

    svg.append("g")
        .classed("y-axis", true)
        .attr("transform", `translate(${marginLeft},0)`)
        .call(d3.axisLeft(y).tickFormat((y) => y.toFixed()))
        .call((g) => g.select(".domain").remove())
        .call((g) =>
            g
                .append("text")
                .attr("x", -marginLeft)
                .attr("y", 10)
                .attr("fill", "currentColor")
                .attr("text-anchor", "start")
                .text("↑ Observations")
        );

    chartContainer.value.appendChild(svg.node());
};

const updateChart = () => {
    const width = 275;
    const height = 275;
    const marginTop = 30;
    const marginRight = 0;
    const marginBottom = 30;
    const marginLeft = 40;

    const x = d3
        .scaleBand()
        .domain(props.data.map((d) => d.name))
        .range([marginLeft, width - marginRight])
        .padding(0.1);

    const y = d3
        .scaleLinear()
        .domain([0, d3.max(props.data, (d) => d.value)])
        .range([height - marginBottom, marginTop]);

    const svg = d3.select(chartContainer.value.querySelector("svg"));

    svg.selectAll("rect")
        .data(props.data)
        .transition()
        .duration(500)
        .attr("x", (d) => x(d.name))
        .attr("y", (d) => y(d.value))
        .attr("height", (d) => y(0) - y(d.value))
        .attr("width", x.bandwidth())
        .select("title")
        .text((d) => d.value);

    svg.select(".x-axis")
        .transition()
        .duration(500)
        .call(d3.axisBottom(x).tickSizeOuter(0));

    svg.select(".y-axis")
        .transition()
        .duration(500)
        .call(d3.axisLeft(y).tickFormat((y) => y.toFixed()))
        .call((g) => g.select(".domain").remove())
        .call((g) => {
            g.selectAll("text") // Select all text elements within the y-axis group
                .attr("x", -marginLeft)
                .attr("y", 10)
                .attr("fill", "currentColor")
                .attr("text-anchor", "start");
            // .text("↑ Observations");
        });
};
</script>

<style scoped>
.chart-container {
    max-width: 100%;
    height: auto;
}
</style>
