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
        observations: [],
        taxa: [],
        users: [],
        locations: [],
    },
    mutations: {
        INIT_OBSERVATIONS(state) {
            console.log('init observations')
            axios.get("/observations")
                .then((response) => state.observations = response.data)
        },
        INIT_TAXA(state){
            console.log('init taxa')
            axios.get("/taxa")
                .then((response) => state.taxa = response.data)
        },
        INIT_USERS(state) {
            console.log('init users')
            axios.get("/users")
                .then((response) => state.users = response.data)
        },
        INIT_LOCATIONS(state) {
            console.log('init locations')
            axios.get("/locations")
                .then((response) => state.locations = response.data)
        }        
    },
    actions: {
        initData({ state }) {
            this.commit("INIT_OBSERVATIONS")
            this.commit("INIT_TAXA")
            this.commit("INIT_USERS")
            this.commit("INIT_LOCATIONS")
        }
    },
    getters: {
    }
})


export default store
