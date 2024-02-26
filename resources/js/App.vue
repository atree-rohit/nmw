<style></style>

<template>
    <div>
        <!-- <species-data /> -->
        <!-- <data-table /> -->
        <years-container />
    </div>
</template>

<script lang="ts" setup>
import SpeciesData from "./components/SpeciesData.vue";
import DataTable from "./components/DataTable.vue";
import YearsContainer from "./components/YearsContainer.vue";
import { onMounted } from "vue";
import { useStore } from "vuex";

const store = useStore();

onMounted(() => {
    // console.clear();
    store.dispatch("initData");
});
</script>
