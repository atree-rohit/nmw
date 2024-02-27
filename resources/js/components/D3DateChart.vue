<style>
.bar-chart-container {
    /* position: absolute; */
}
.x-axis,
.y-axis {
    font-size: 1.5rem;
}

.bar-chart-container .controls-container {
    /* top: 180px; */
}
.bar-chart-container:hover .controls-container {
    opacity: 1;
}
</style>

<template>
    <div class="bar-chart-container">
        <div class="controls-container">
            <button
                type="button"
                class="btn btn-sm"
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
        </div>
        <div
            class="chart-container p-2 bg-light rounded"
            ref="chartContainer"
        ></div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from "vue";
import * as d3 from "d3";

import { useStore } from "vuex";

const store = useStore();

const nmw_data = computed(() => store.state.nmw_data);
const data_modes = ["Observations", "Taxa", "Users"];
const selected_data_mode = ref(0);

const data = computed(() => {
    const op = [];
    const all_data = nmw_data.value[props.year]?.dates;
    if (!all_data || props.year == 0) return op;
    Object.keys(all_data).forEach((d) => {
        const date_arr = d.split("-");
        op.push({
            name: date_arr[2],
            value: all_data[d][
                data_modes[selected_data_mode.value].toLowerCase()
            ],
        });
    });
    op.sort((a, b) => a - b);
    return op;
});

const chartContainer = ref(null);
const props = defineProps({
    year: {
        type: Number,
        required: true,
    },
});

onMounted(() => renderChart());

watch(
    () => props.year,
    (newData, oldData) => {
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
        .domain(data.value.map((d) => d.name))
        .range([margin.left, width])
        .padding(0.1);

    const y = d3
        .scaleLinear()
        .domain([0, d3.max(data.value, (d) => d.value)])
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
        .data(data.value)
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
