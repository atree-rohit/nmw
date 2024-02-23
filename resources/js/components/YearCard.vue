<style></style>

<template>
    <div class="card border border-dark">
        <div class="card-body">
            <h1 class="text-center card-title">{{ year }}</h1>
            <div class="d-flex justify-content-around">
                <div
                    class="card"
                    v-for="(value, total_key) in totals"
                    :key="total_key"
                >
                    <div class="card-body text-center py-2">
                        <h4 class="card-title">{{ value }}</h4>
                        <div class="card-body">{{ total_key }}</div>
                    </div>
                </div>
            </div>
        </div>
        <id-level-chart :year="year" />
        <temporal-chart :year="year" />
        <map-container :year="year" />
    </div>
</template>

<script lang="js" setup>
import { ref, computed, watch } from "vue";
import { useStore } from "vuex";

import IdLevelChart from "./IdLevelChart.vue";
import TemporalChart from "./TemporalChart.vue";
import MapContainer from "./MapContainer.vue";

const store = useStore();

const props = defineProps({
    year: {
        type: Number,
        required: true,
    },
});

const nmw_data = computed(() => store.state.nmw_data);

const totals = ref({
    observations: 0,
    taxa: 0,
    users: 0,
});

watch(
    () => nmw_data.value,
    (newVal) => {
        if (Object.keys(newVal).length) {
            const current_year = newVal[props.year];
            totals.value = {
                observations: current_year.total_observations,
                taxa: current_year.total_taxa,
                users: current_year.total_users,
            };
        }
    }
);
</script>
