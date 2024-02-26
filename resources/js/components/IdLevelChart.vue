<style></style>

<template>
    <div class="d-flex flex-column border border-info m-1 rounded p-1 gap-2">
        <D3PieChart :data="data" class="mx-auto" />
        <div class="btn-group">
            <button
                type="button"
                class="btn btn-sm"
                v-for="(mode, m_id) in data_modes"
                :key="m_id"
                :class="
                    selected_data_mode == m_id
                        ? 'btn-success'
                        : 'btn-outline-secondary'
                "
                v-text="mode"
                @click="selected_data_mode = m_id"
            />
        </div>
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
