<style></style>

<template>
    <div class="d-flex flex-column m-1 rounded p-1 gap-2">
        <D3PieChart :year="year" class="mx-auto" />
    </div>
</template>

<script lang="js" setup>
import { onMounted, computed, ref, watch } from "vue";
import * as d3 from "d3";
import { useStore } from "vuex";

import D3PieChart from "./D3PieChart.vue";

const store = useStore();
const nmw_data = computed(() => store.state.nmw_data);
const id_levels_data = computed(()=> nmw_data.value[props.year]?.count_id_levels)
const data_modes = ["Observations", "Taxa"]
const selected_data_mode = ref(0)

const data = computed(() => {
    let op = []
    if(!id_levels_data.value){
        return op
    }
    const taxa_levels = ["order","superfamily","family","subfamily","tribe","subtribe","genus","species","subspecies","complex"];
    taxa_levels.forEach((t) => {
        if(id_levels_data.value[t]){
            op.push({
                name: t,
                value: id_levels_data.value[t][data_modes[selected_data_mode.value].toLowerCase()]
            })

        }
    })
    return op

})

const props = defineProps({
    year:  {
        type: Number,
        required: true
    }
})
</script>
