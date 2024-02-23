<template>
    <div class="d-flex flex-column border border-info m-1 rounded p-1 gap-2">
        <template v-if="showD3Map">
            <d3-map
                :level="selected_map_level"
                :data="locations"
                :year="year"
                :labels="showLabels"
            />
        </template>
        <button
            class="btn btn-sm"
            :class="showLabels ? 'btn-success' : 'btn-outline-danger'"
            @click="showLabels = !showLabels"
        >
            {{ showLabels ? "Hide" : "Show" }} Labels
        </button>
        <div class="btn-group">
            <button
                type="button"
                class="btn btn-sm"
                v-for="(mode, m_id) in map_modes"
                :key="m_id"
                :class="
                    selected_mode == m_id
                        ? 'btn-success'
                        : 'btn-outline-secondary'
                "
                v-text="mode"
                @click="selected_mode = m_id"
            />
        </div>
    </div>
</template>

<script lang="js" setup>
import {ref, computed, onMounted } from "vue";
import { useStore } from "vuex";
import D3Map from "./D3Map.vue"

const store = useStore();

const props = defineProps({
    year: {
        type: Number,
        required: true,
    },
});

const nmw_data = computed(() => store.state.nmw_data);
const map_modes = ["regions", "states", "districts"]
const selected_mode = ref(0)
const selected_map_level = computed(() => map_modes[selected_mode.value])
const showD3Map = ref(false);
const showLabels = ref(true);


const locations = computed(() => {
    const op = [];
    if (Object.keys(nmw_data.value).length == 0) return op;

    const current_locations = nmw_data.value[props.year].locations;
    if(selected_mode.value === 0){
        Object.keys(current_locations).forEach((region) => {
            op.push({
                name: region,
                value: current_locations[region].region_total,
            })
        })
    } else if (selected_mode.value === 1) {
        Object.keys(current_locations).forEach((region) => {
            Object.keys(current_locations[region]).forEach((state) => {
                if(state !== "region_total"){
                    op.push({
                        name: state,
                        value: current_locations[region][state].state_total,
                    })
                }
            })
        })
    } else if (selected_mode.value === 2) {
        Object.keys(current_locations).forEach((region) => {
            Object.keys(current_locations[region]).forEach((state) => {
                if(state !== "region_total"){
                    Object.keys(current_locations[region][state]).forEach((district) => {
                        if(district !== "state_total"){
                            op.push({
                                name: district,
                                value: current_locations[region][state][district],
                            })
                        }
                    });
                }
            });
        });
    }
    return op.sort((a,b) => b.value - a.value)
})

onMounted(() => {
    setTimeout(() => {
        showD3Map.value = true;
        console.log("loaded")
    }, 100);
});
</script>
