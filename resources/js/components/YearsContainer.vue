<style scoped>
.page-container {
    display: flex;
    flex-direction: column;
    height: 100vh;
    border: 1px solid red;
}

.button-group {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    align-items: center;
    padding: 1rem;
    padding-bottom: 0;
}

.button {
    margin-right: 0.5rem;
    padding: 0.5rem 1rem;
    cursor: pointer;
    border-radius: 0.5rem 0.5rem 0 0;
    text-decoration: none;
    transition: all 250ms;
}

.button:hover {
    background-color: #aaa;
}

.button.selected {
    background-color: #ccc;
}

.data-container {
    display: grid;
    grid-template-areas: "year"
}

.data-container {
    flex-grow: 1;
    padding: 5px;
    display: grid;
    grid-template-columns: 0.5fr auto 1.5fr;
    grid-template-rows: 0.2fr 1.3fr 1.5fr;
    gap: 5px;
    grid-auto-flow: row;
    grid-template-areas:
        "year map pie-chart"
        "stats map pie-chart"
        "stats map bar-chart"
        "stats map bar-chart";
}

.data-container>div {
    outline: 1px solid rgb(150, 0, 0, .5);
    border-radius: 0.5rem;
    /* margin: 3px; */
}

.year {
    grid-area: year;
    font-size: 3vw;
    text-align: center;
}

.map-controls {
    grid-area: map-controls;
}

.chart-controls {
    grid-area: chart-controls;
}

.stats {
    grid-area: stats;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-evenly;
}

.stat-card {
    background-color: rgba(230, 230, 230, .75);
    border: 1px solid rgba(200, 220, 220, 0.75);
    border-radius: 1rem;
    width: auto;
    min-width: 90%;
    display: grid;
    gap: 0.5rem;
    padding: 0.5rem 0;
    text-align: center;

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
}

.pie-chart {
    grid-area: pie-chart;
}

.bar-chart {
    grid-area: bar-chart;
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
        background-color: rgba(0, 230, 230, .75);
    }
}
</style>

<template>
    <div class="page-container">
        <div class="button-group">
            <a class="button" :class="{ 'selected': selectedYear == 0 }" v-text="`All`" @click="selectYear(0)" />
            <a class="button" v-for="year in years" :key="year" :class="{ 'selected': selectedYear == year }" v-text="year"
                @click="selectYear(year)" />
        </div>
        <div class="data-container">
            <div class="year">{{ selectedYear }}</div>
            <div class="map-controls"></div>
            <div class="chart-controls"></div>
            <div class="stats">
                <div class="stat-card" v-for="(value, label) in totals" :key="label">
                    <span class="card-value">{{ value }}</span>
                    <span class="card-label">{{ label }}</span>
                </div>
            </div>
            <div class="map">
                <map-container :year="selectedYear" />
            </div>
            <div class="pie-chart">
                <id-level-chart :year="selectedYear" />
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

import IdLevelChart from "./IdLevelChart.vue";
import TemporalChart from "./TemporalChart.vue";
import MapContainer from "./MapContainer.vue";

const store = useStore();

const startYear = 2012;
const endYear = 2023;
const years = Array.from(
    Array(endYear - startYear + 1),
    (_, index) => startYear + index
);
const selectedYear = ref(0);






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


// onMounted(() => {
//     for (let i = 2012; i < 2024; i++) {
//         years.value.push(i)
//     }
// })
const selectYear = (year) => {
    selectedYear.value = year
}
</script>
