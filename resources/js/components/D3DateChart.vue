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
    year: {
        type: Number,
        required: true,
    },
});

onMounted(() => renderChart());

watch(
    () => [props.data, props.year],
    ([newData, newYear], [oldData, oldYear]) => {
        const svg = chartContainer.value.querySelector("svg");
        if (svg) {
            svg.remove();
        }
        renderChart();
    }
);

const margin = { top: 30, right: 0, bottom: 30, left: 40 };
const width = window.innerWidth / 1.5 - margin.left - margin.right;
const height = window.innerHeight / 1.5 - margin.top - margin.bottom;

const renderChart = () => {
    const x = d3
        .scaleBand()
        .domain(props.data.map((d) => d.name))
        .range([margin.left, width])
        .padding(0.1);

    const y = d3
        .scaleLinear()
        .domain([0, d3.max(props.data, (d) => d.value)])
        .range([height, margin.top]);

    const svg = d3
        .create("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .attr("viewBox", [
            0,
            0,
            width + margin.left + margin.right,
            height + margin.top + margin.bottom,
        ])
        .attr("style", "max-width: 100%; height: auto;");

    const g = svg
        .append("g")
        .attr("transform", `translate(${margin.left},${margin.top})`);

    g.selectAll("rect")
        .data(props.data)
        .join("rect")
        .attr("x", (d) => x(d.name))
        .attr("y", (d) => y(d.value))
        .attr("height", (d) => y(0) - y(d.value))
        .attr("width", x.bandwidth())
        .attr("fill", "steelblue")
        .append("title")
        .text((d) => `${d.value}`);

    g.append("g")
        .classed("x-axis", true)
        .attr("transform", `translate(0,${height})`)
        .call(d3.axisBottom(x).tickSizeOuter(0));

    g.append("g")
        .classed("y-axis", true)
        .call(d3.axisLeft(y).tickFormat((y) => y.toFixed()))
        .call((g) => g.select(".domain").remove())
        .call((g) => {
            g.selectAll("text")
                .attr("x", -margin.left)
                .attr("y", 10)
                .attr("fill", "currentColor")
                .attr("text-anchor", "start");
        });

    chartContainer.value.appendChild(svg.node());
};
</script>

<style scoped>
/* .chart-container {
    max-width: 100%;
    height: auto;
} */
</style>
