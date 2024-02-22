<style></style>

<template>
    <div class="d-flex flex-column border border-info m-1 rounded p-1 gap-2">
        <div class="text-center h3">{{ year }}</div>
        <D3PieChart :data="data" />
        <div class="btn-group">
            <button
                type="button"
                class="btn btn-sm"
                :class="
                    selected_data_mode == 0
                        ? 'btn-success'
                        : 'btn-outline-secondary'
                "
                @click="selected_data_mode = 0"
            >
                Observations
            </button>
            <button
                type="button"
                class="btn btn-sm"
                :class="
                    selected_data_mode == 1
                        ? 'btn-success'
                        : 'btn-outline-secondary'
                "
                @click="selected_data_mode = 1"
            >
                Taxa
            </button>
        </div>
        <D3DateChart :data="dates_data" />
    </div>
</template>

<script lang="js" setup>
import { onMounted, computed, ref, watch } from "vue";
import * as d3 from "d3";
import { useStore } from "vuex";

import D3PieChart from "./D3PieChart.vue";
import D3DateChart from "./D3DateChart.vue";

const store = useStore();
const nmw_data = computed(() => store.state.nmw_data);
const id_levels_data = computed(()=> nmw_data.value[props.year]?.count_id_levels)
const data_modes = ["observations", "taxa"]
const selected_data_mode = ref(0)


const dates_data = computed(()=> {
    const op = []
    const all_data = nmw_data.value[props.year]?.dates
    if(!all_data) return op
    Object.keys(all_data).forEach((d) => {
        const date_arr = d.split("-")
        op.push({
            name: date_arr[2],
            value: all_data[d][data_modes[selected_data_mode.value]]
        })
    })
    op.sort((a,b) => a-b)
    return op
})




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
                value: id_levels_data.value[t][data_modes[selected_data_mode.value]]
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
