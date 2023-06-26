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
        expanded_data: [],
        regional_data: [],
        polygon_data: [],
        selected_years: [2012,2013,2014,2015,2016,2017,2018,2019,2020,2021,2022],
    },
    mutations: {
        SET_OBSERVATIONS(state, observations) {
            state.observations = observations
        },
        SET_TAXA(state, taxa){
            state.taxa = taxa
        },
        SET_USERS(state, users) {
            state.users = users
        },
        SET_LOCATIONS(state, locations) {
            state.locations = locations
        },
        SET_EXPANDED_DATA(state, data){
            state.expanded_data = data
        },
        SET_REGIONAL_DATA(state, regional_data) {
            state.regional_data = regional_data
        },
        SET_POLYGON_DATA(state, polygon_data) {
            state.polygon_data = polygon_data
        },
        SET_SELECTED_YEARS(state, year) {
            if (state.selected_years.includes(year)) {
                state.selected_years = state.selected_years.filter((y) => y != year)
            } else {
                state.selected_years.push(year)
            }
        }
    },
    actions: {
        async initData({ commit, dispatch }) {
            try {
                await dispatch("initObservations")
                await dispatch("initTaxa")
                await dispatch("initUsers")
                await dispatch("initLocations")
                await dispatch("initExpandedData")
                await dispatch("initRegionalData")
                await dispatch("initPolygonData")
                console.log("init completed")
            } catch(error) {
                console.error("Initialization error: ", error)
            }
        },
        async setYear({commit, dispatch}, year){
            commit("SET_SELECTED_YEARS", year)
            await dispatch("initExpandedData")
            await dispatch("initRegionalData")
            await dispatch("initPolygonData")
        },
        async initObservations({ commit }) {
            // console.log("Init observations")
            const observations = await axios.get("/nmw/observations")
            commit("SET_OBSERVATIONS", observations.data)
        },
        async initTaxa({ commit }) {
            // console.log("Init taxa")
            const taxa = await axios.get("/nmw/taxa")
            commit("SET_TAXA", taxa.data)
        },
        async initUsers({ commit }) {
            // console.log("Init users")
            const users = await axios.get("/nmw/users")
            commit("SET_USERS", users.data)
        },
        async initLocations({ commit }) {
            // console.log("Init locations")
            const locations = await axios.get("/nmw/locations")
            commit("SET_LOCATIONS", locations.data)
        },
        async initExpandedData({ commit, state}) {
            let ob_fields = ["id", "observed_on", "license",  "nmw", "user_id", "taxa_id", "location_id", "created_at", "updated_at"]
            let op = []
            op = state.observations.map((o) => {
                let x = {}
                ob_fields.forEach((f, k) => {
                    x[f] = o[k]
                })
                x.user_name = state.users[o[4]].name
                x.user_login = state.users[o[4]].login
                x.scientific_name = state.taxa[o[5]].name
                x.common_name = state.taxa[o[5]].common_name
                x.region = state.locations[o[6]].region
                x.state = state.locations[o[6]].state
                x.district = state.locations[o[6]].district
                return x
            }).filter((o) => state.selected_years.includes(o.nmw))
            commit("SET_EXPANDED_DATA", op)
        },
        async initRegionalData({ commit, state }) {
            commit("SET_REGIONAL_DATA", d3.groups(state.expanded_data, d => d.region, d => d.state, d => d.district))
        },
        async initPolygonData({ commit, state }) {
            let polygon_data = {
                regions: {},
                states: {},
                districts: {}
            }
            //regional stats
            d3.groups(state.expanded_data, d => d.region).forEach((d) => {
                polygon_data.regions[d[0]] = {
                    observations: d[1].length,
                    taxa: d3.rollup(d[1], v => new Set(v.map(d => d.taxa_id)).size, d => d.taxa_id).size,
                    users: d3.rollup(d[1], v => new Set(v.map(d => d.user_id)).size, d => d.user_id).size,
                    locations: d3.rollup(d[1], v => new Set(v.map(d => d.location_id)).size, d => d.location_id).size,
                    nmw:  [...new Set(d[1].map(d => d.nmw))].sort(),
                }
            })
            //state stats
            d3.groups(state.expanded_data, d => d.state).forEach((d) => {
                polygon_data.states[d[0]] = {
                    observations: d[1].length,
                    taxa: d3.rollup(d[1], v => new Set(v.map(d => d.taxa_id)).size, d => d.taxa_id).size,
                    users: d3.rollup(d[1], v => new Set(v.map(d => d.user_id)).size, d => d.user_id).size,
                    locations: d3.rollup(d[1], v => new Set(v.map(d => d.location_id)).size, d => d.location_id).size,
                    nmw:  [...new Set(d[1].map(d => d.nmw))].sort(),
                }
            })
            //district stats
            d3.groups(state.expanded_data, d => d.district).forEach((d) => {
                polygon_data.districts[d[0]] = {
                    observations: d[1].length,
                    taxa: d3.rollup(d[1], v => new Set(v.map(d => d.taxa_id)).size, d => d.taxa_id).size,
                    users: d3.rollup(d[1], v => new Set(v.map(d => d.user_id)).size, d => d.user_id).size,
                    locations: d3.rollup(d[1], v => new Set(v.map(d => d.location_id)).size, d => d.location_id).size,
                    nmw:  [...new Set(d[1].map(d => d.nmw))].sort(),
                }
            })
            commit("SET_POLYGON_DATA", polygon_data)
        }
    },
    //Observations fields ["id", "observed_on", "license", "image_url", "nmw", "user_id", "taxa_id", "location_id", "created_at", "updated_at"]
    getters: {
    }
})


export default store
