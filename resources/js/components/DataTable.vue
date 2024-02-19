<style></style>

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
                    <!-- <td v-text="year_stats.total_observations" />
                    <td v-text="year_stats.total_taxa" /> -->
                    <td>
                        <data-table-taxa-levels
                            :taxa_levels_data="year_stats"
                            :year="year"
                        />
                    </td>
                    <!-- <td v-text="year_stats.total_users" />
                    <td>
                        <data-table-locations
                            :locations_data="year_stats.locations"
                        />
                    </td>
                    <td>
                        <data-table-dates :dates_data="year_stats.dates" />
                    </td> -->
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

// Destructure the initData action from the store
const store = useStore();

const geojson = computed(() => store.state.geojson);
const nmw_data = computed(() => store.state.nmw_data);
</script>
