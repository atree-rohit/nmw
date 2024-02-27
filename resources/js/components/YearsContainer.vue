<style>
:root {
    --clr1: #64acfa;
    --clr2: #5593d4;
    --clr3: #66b0ff;
    --clr4: #477bb3;
    --clr5: #00458f;
    --clr6: #2d4d70;
}
.page-container {
    display: flex;
    flex-direction: column;
    height: 100vh;
    /* border: 1px solid red; */
}

.button-group {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    align-items: center;
    padding: 1rem;
    /* padding-bottom: 0; */
    background-color: var(--clr1);
}

.button {
    margin-right: 0.5rem;
    padding: 0.5rem 1rem;
    cursor: pointer;
    border-radius: 2rem;
    border-color: transparent;
    text-decoration: none;
    transition: all 250ms;
}

.button:hover {
    background-color: #95a4f0;
}

.button.selected {
    /* border-radius: 2rem 2rem 0 0; */
    background-color: #31a557;
    color: white;
    flex-grow: 0.2;
}

.data-container {
    grid-template-areas: "year";
    flex-grow: 1;
    /* padding: 5px; */
    display: grid;
    grid-template-columns: 0.5fr auto 1.5fr;
    grid-template-rows: 0.2fr 1.3fr 1.5fr;
    /* gap: 5px; */
    grid-auto-flow: row;
    grid-template-areas:
        "year map pie-chart"
        "stats map pie-chart"
        "stats map bar-chart"
        "stats map bar-chart";
}

.year {
    grid-area: year;
    font-size: 3vw;
    text-align: center;
    background-color: var(--clr2);
    color: white;
    border-bottom: 1px solid white;
}

.stats {
    background-color: var(--clr2);
    grid-area: stats;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-evenly;
}

.stat-card {
    background-color: rgba(255, 255, 255);
    border: 1px solid rgba(255, 255, 255);
    border-radius: 1rem;
    width: auto;
    min-width: 90%;
    display: grid;
    gap: 0.5rem;
    padding: 0.5rem 0;
    text-align: center;
}

.card-value,
.card-label {
    color: var(--clr4);
}
.card-value {
    font-size: 2rem;
    font-weight: 300;
}

.card-label {
    text-transform: capitalize;
}

.map {
    grid-area: map;
    background: hsl(200, 25%, 25%);
    background: var(--clr6);
}

.pie-chart {
    grid-area: pie-chart;
    display: grid;
    align-items: center;
    background: var(--clr5);
}

.bar-chart {
    grid-area: bar-chart;
    display: grid;
    align-items: center;
    background: var(--clr5);
}

@media only screen and (max-width: 600px) {
    .data-container {
        flex-grow: 1;
        padding: 5px;
        display: grid;
        grid-template-columns: 1fr;
        grid-template-rows: auto;
        grid-auto-flow: row;
        grid-template-areas:
            "year"
            "stats"
            "map"
            "pie-chart"
            "bar-chart";
    }

    .stat-card {
        background-color: rgba(0, 230, 230, 0.75);
    }
}
</style>

<template>
    <div class="page-container">
        <div class="button-group">
            <button
                class="button"
                :class="{ selected: selectedYear == 0 }"
                v-text="`All`"
                @click="selectYear(0)"
            />
            <button
                class="button"
                v-for="year in years"
                :key="year"
                :class="{ selected: selectedYear == year }"
                v-text="year"
                @click="selectYear(year)"
            />
            <button class="button" @click="toggleCycleYears">
                {{ cycleYears ? "Stop Cycling" : "Cycle Years" }}
            </button>
        </div>
        <div class="data-container">
            <div class="year">
                {{ selectedYear ? selectedYear : "All" }}
            </div>
            <div class="stats">
                <div
                    class="stat-card"
                    v-for="(value, label) in totals"
                    :key="label"
                >
                    <span class="card-value">{{ value }}</span>
                    <span class="card-label">{{ label }}</span>
                </div>
            </div>
            <div class="map">
                <d3-map :year="selectedYear" />
            </div>
            <div class="pie-chart">
                <d3-pie-chart :year="selectedYear" />
            </div>
            <div class="bar-chart">
                <temporal-chart :year="selectedYear" />
            </div>
        </div>
    </div>
</template>

<script lang="js" setup>
import { ref, computed, watch, onMounted } from "vue";
import { useStore } from "vuex";

import D3PieChart from "./D3PieChart.vue";
import TemporalChart from "./TemporalChart.vue";
import D3Map from "./D3Map.vue";

const store = useStore();

const startYear = 2012;
const endYear = 2023;
const years = Array.from(
    Array(endYear - startYear + 1),
    (_, index) => startYear + index
);
const selectedYear = ref(0);
const cycleYears = ref(false);

const nmw_data = computed(() => store.state.nmw_data);
const totals = computed(() => {
    const op = {
        observations: 0,
        taxa: 0,
        users: 0
    }
    const keys = Object.keys(nmw_data.value)
    if (keys.length == 0) return op
    const year_data = nmw_data.value[selectedYear.value]
    op.observations = year_data.total_observations
    op.taxa = year_data.total_taxa
    op.users = year_data.total_users

    return op
})

const selectYear = (year) => {
    selectedYear.value = year
}

const toggleCycleYears = () => {
    cycleYears.value = !cycleYears.value;
};

// Watch for changes in cycleYears and start/stop cycling accordingly
watch(cycleYears, (newVal) => {
    if (newVal) {
        let currentIndex = years.indexOf(selectedYear.value);
        const cycleInterval = setInterval(() => {
            // Move to the next year
            currentIndex = (currentIndex + 1) % years.length;
            selectedYear.value = years[currentIndex];
        }, 1000);

        // Stop cycling when cycleYears is false
        watch(cycleYears, (newVal) => {
            if (!newVal) {
                clearInterval(cycleInterval);
            }
        });
    }
});
</script>
