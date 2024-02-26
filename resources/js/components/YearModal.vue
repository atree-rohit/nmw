<template>
    <map-container :year="year" />
    <div class="accordion" id="year-accordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="id_level-container">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#id_level" aria-expanded="false" aria-controls="id_level">
                    ID Level
                </button>
            </h2>
            <div id="id_level" class="accordion-collapse collapse" aria-labelledby="id_level-container"
                data-bs-parent="#year-accordion">
                <div class="accordion-body">
                    <id-level-chart :year="year" />
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="temporal-container">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#temporal" aria-expanded="false" aria-controls="temporal">
                    Temporal
                </button>
            </h2>
            <div id="temporal" class="accordion-collapse collapse" aria-labelledby="temporal-container"
                data-bs-parent="#year-accordion">
                <div class="accordion-body">
                    <temporal-chart :year="year" />
                </div>
            </div>
        </div>
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