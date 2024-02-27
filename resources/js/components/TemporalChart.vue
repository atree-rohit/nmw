<style></style>

<template>
    <div class="d-flex flex-column m-1 rounded p-1 gap-2">
        <D3DateChart :data="data" :year="year" class="mx-auto" />
    </div>
</template>

<script lang="js" setup>
import { onMounted, computed, ref, watch } from "vue";
import * as d3 from "d3";
import { useStore } from "vuex";

import D3DateChart from "./D3DateChart.vue";

const store = useStore();
const nmw_data = computed(() => store.state.nmw_data);
const data_modes = ["Observations", "Taxa", "Users"]
const selected_data_mode = ref(0)


const data = computed(()=> {
    const op = []
    const all_data = nmw_data.value[props.year]?.dates
    if(!all_data || props.year == 0) return op
    Object.keys(all_data).forEach((d) => {
        const date_arr = d.split("-")
        op.push({
            name: date_arr[2],
            value: all_data[d][data_modes[selected_data_mode.value].toLowerCase()]

        })
    })
    op.sort((a,b) => a-b)
    return op
})

const props = defineProps({
    year:  {
        type: Number,
        required: true
    }
})
</script>
