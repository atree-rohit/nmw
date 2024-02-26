import { createStore } from 'vuex'
import * as d3 from 'd3'
import axios from 'axios'
import moment from 'moment'
import districts from './geojson/districts.json'
import states from './geojson/states.json'
import regions from './geojson/regions.json'

const store = createStore({
    state: {
        geojson: {
            districts: districts,
            states: states,
            regions: regions
        },
        nmw_data: {},
    },
    mutations: {
        SET_NMW_DATA(state, nmw_data) {
            state.nmw_data = nmw_data
        }
    },
    actions: {
        async initData({ commit, dispatch }) {
            try {
                await dispatch("initNMWData")
                // await dispatch("initTaxa")
                // await dispatch("initUsers")
                // await dispatch("initLocations")
                // await dispatch("initExpandedData")
                // await dispatch("initRegionalData")
                // await dispatch("initPolygonData")
                console.log("init completed")
            } catch(error) {
                console.error("Initialization error: ", error)
            }
        },
        async initNMWData({ commit }) {
            const observations = await axios.get("nmw_data")
            
            commit("SET_NMW_DATA", JSON.parse(observations.data))
        }
    },
    //Observations fields ["id", "observed_on", "license", "image_url", "nmw", "user_id", "taxa_id", "location_id", "created_at", "updated_at"]
    getters: {
    }
})


export default store
