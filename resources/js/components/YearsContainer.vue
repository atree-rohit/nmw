<style scoped>
.year-card {
    width: 90vw;
    font-size: 3rem;
}

.total-card {
    flex-grow: 2;
}

.total-card .card-text {
    text-transform: capitalize;
}
</style>

<template>
    <div class="container d-flex flex-column text-center">

        <div class="btn-group">
            <button type="button" class="btn btn-sm mx-1 rounded" :class="selected_year == null
                ? 'btn-success'
                : 'btn-outline-secondary'
                " @click="selected_year = null">All</button>
            <button type="button" class="btn btn-sm mx-1 rounded" v-for="year in years" :key="year" :class="selected_year == year
                ? 'btn-success'
                : 'btn-outline-secondary'
                " v-text="year" @click="selected_year = year" />
        </div>
        <div class="d-flex flex-column">
            <div class="year-card">
                <div class="selected-value" v-text="(selected_year) ? selected_year : 'All'"></div>
            </div>
            <div class="totals-container d-flex">
                <div class="card total-card" v-for="(value, label) in totals" :key="label">
                    <div class="card-body">
                        <h5 class="card-title">{{ value }}</h5>
                        <p class="card-text">{{ label }}</p>
                    </div>
                </div>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm mx-1 rounded" v-for="chart in charts" :key="chart" :class="selected_chart == chart
                    ? 'btn-success'
                    : 'btn-outline-secondary'
                    " v-text="chart" @click="selected_chart = chart" />
            </div>
            {{ selected_chart }}
            <div class="charts-container border border-danger">
                <map-container :year="selected_year" v-if="selected_chart == 'Map'"/>
                <id-level-chart :year="selected_year" v-else-if="selected_chart == 'ID_Level_Chart'"/>
                <temporal-chart :year="selected_year" v-else-if="selected_chart == 'Dates_Chart'"/>

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

const startYear = 2012;
const endYear = 2023;
const years = Array.from(
    Array(endYear - startYear + 1),
    (_, index) => startYear + index
);
const charts = ["Map", "ID_Level_Chart", "Dates_Chart"]
const selected_chart = ref("ID_Level_Chart")

const selected_year = ref(2012);

const store = useStore();

const nmw_data = computed(() => store.state.nmw_data);
const totals = computed(() => {
    const op = {
        observations: 0,
        taxa: 0,
        users: 0
    }
    const keys = Object.keys(nmw_data.value)
    if (keys.length == 0) return op
    if (selected_year.value == null) {
        keys.forEach((year) => {
            const year_data = nmw_data.value[year]
            op.observations += year_data.total_observations
            op.taxa += year_data.total_taxa
            op.users += year_data.total_users
        })
        return op
    }
    const year_data = nmw_data.value[selected_year.value]
    op.observations = year_data.total_observations
    op.taxa = year_data.total_taxa
    op.users = year_data.total_users

    return op
})

</script>