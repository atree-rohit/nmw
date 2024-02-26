<style>
.year-card {
    /* transform: scale(3); 
    margin-top: 10rem; */
    border: 2px solid hsl(187 49% 43% / 1) !important;
    border-radius: 1rem;
}

.card-body {
    padding: 0.5rem !important;
}

.card-title {
    font-weight: 100;
    font-size: 3.5rem;
    /* background: red; */
    margin: 0 !important;
}

.stat {
    /* border: 1px solid green; */
    padding: 0 1rem;
}

.stat-value {
    /* border: 1px solid purple; */
    margin: 0;
}

.stat-label {
    text-transform: capitalize;
    /* border: 1px solid blue; */
}
</style>

<template>
    <div class="year-card">
        <div class="card-body">
            <div class="stats-container d-flex">
                <h1 class="card-title">{{ year }}</h1>
                <div class="stat" v-for="(value, total_key) in totals" :key="total_key">
                    <div class="stat-label">{{ total_key }}</div>
                    <h5 class="stat-value">{{ value }}</h5>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="js" setup>
import { ref, computed, watch } from "vue";
import { useStore } from "vuex";

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
