<style></style>

<template>
    <div>
        <!-- <species-data /> -->
        <data-table />
    </div>
</template>

<script lang="ts" setup>
import DataTable from "./components/DataTable.vue";
import SpeciesData from "./components/SpeciesData.vue";
import { onMounted } from "vue";
import { useStore } from "vuex";

const store = useStore();

onMounted(() => {
    console.clear();
    store.dispatch("initData");
});
</script>
