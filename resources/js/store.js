import { createStore } from 'vuex'
import districts from "./geojson/districts.json"
import states from "./geojson/states.json"
import regions from "./geojson/regions.json"

const store = createStore({
    state: {
        test: 111,
        geojson: {
            districts: districts,
            states: states,
            regions: regions
        }
    },
    mutations: {
    },
    actions: {
    },
    getters: {
    }
})

export default store