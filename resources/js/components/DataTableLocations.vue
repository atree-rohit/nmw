<style></style>
<template>
    <table class="table table-sm border border-success">
        <thead class="table-success">
            <tr>
                <th>Name</th>
                <th>Observations</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(region_data, region) in locations_data" :key="region">
                <td v-text="region" />
                <td v-text="region_data.region_total" />
                <td>
                    <button
                        class="btn btn-sm btn-outline-primary"
                        @click="showStateData(region_data)"
                    >
                        State Data
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script lang="js" setup>
import { onMounted, computed,ref } from "vue";
import { useStore } from "vuex";

const props = defineProps({
    locations_data: {
        type: Object,
        required: true
    }
})

const store = useStore();

const geojson = computed(() => store.state.geojson);

const nested_data = ref({})

const showStateData = (data) => {
    console.log(data)
}
</script>
