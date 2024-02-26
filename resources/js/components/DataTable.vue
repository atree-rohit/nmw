<<<<<<< HEAD
<style scoped>
.overall-data{
    background:black;
    color:green;
    text-align: center;
    font-size:1.5rem;
    padding: 1rem;
}
.overall-data b{
    font-size: 2.25rem;
}
.instructions{
    font-size:2.0rem;
    text-align: center;
    padding: 2rem 0;
}
.stats-table-container{
    padding: 1rem;
    height: 87.5%;
    overflow: auto;
}
.stats-table table {
  border-collapse: collapse;
}

.stats-table th, td {
  padding: 10px;
  border: 1px solid transparent !important;
  text-align: center;
}


.stats-table th {
    background-color: #222;
    color: white;
    font-weight: bold;
}

.stats-table th.clickable div{
    cursor: pointer;
    display:flex;
    justify-content: center;
}
.stats-table th.clickable div:hover{
    background-color: #ddd;
}
.stats-table tr:nth-of-type(even) {
  background-color: #444;
}

.stats-table th{
    background: white;
    color: black;
    border-left: 2px solid red;
    border-right: 2px solid red;
    padding: 0.25rem .5rem; 
}
.stats-table .subtitle-row td{
    background: rgba(0, 128, 0, .5);
}
.stats-table tr:has(td):hover:not(.subtitle-row){
    background: rgb(255, 241, 116);
    color: black;
}

.stats-table tr, 
.stats-table th, 
.stats-table td{
    transition: all 150ms ease-in-out;
}
</style>
=======
<style></style>
>>>>>>> main

<template>
    <div>
        <table class="table border border-danger">
            <thead class="table-danger">
                <tr>
                    <th>Year</th>
                    <th>Observations</th>
                    <th>Taxa</th>
                    <th>ID Levels</th>
                    <th>Users</th>
                    <th>Locations</th>
                    <th>Dates</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(year_stats, year) in nmw_data" :key="year">
                    <td v-text="year" />
                    <td v-text="year_stats.total_observations" />
                    <td v-text="year_stats.total_taxa" />
                    <td>
                        <data-table-taxa-levels
                            :taxa_levels_data="year_stats"
                            :year="year"
                        />
                    </td>
                    <td v-text="year_stats.total_users" />
                    <td>
                        <data-table-locations
                            :locations_data="year_stats.locations"
                        />
                    </td>
                    <td>
                        <data-table-dates :dates_data="year_stats.dates" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script lang="ts" setup>
import { onMounted, computed } from "vue";
import { useStore } from "vuex";

import DataTableTaxaLevels from "./DataTableTaxaLevels.vue";
import DataTableLocations from "./DataTableLocations.vue";
import DataTableDates from "./DataTableDates.vue";
import YearCard from "./YearCard.vue";

const store = useStore();

const geojson = computed(() => store.state.geojson);
const nmw_data = computed(() => store.state.nmw_data);
const startYear = 2022;
const endYear = 2023;
const years = Array.from(
    Array(endYear - startYear + 1),
    (_, index) => startYear + index
);
</script>
