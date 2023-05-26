import { createStore } from 'vuex'
import * as d3 from 'd3'
import districts from './geojson/districts.json'
import states from './geojson/states.json'
import regions from './geojson/regions.json'
import data_1 from './inat_data/to_2020_01_01.json'
import data_2 from './inat_data/2020_01_01_to_2021_06_01.json'
import data_3 from './inat_data/2021_06_01_to_2022_06_01.json'

const store = createStore({
    state: {
        geojson: {
            districts: districts,
            states: states,
            regions: regions
        },
        inat_data: [data_1, data_2, data_3],
        observations: [],
        users: [],
        taxa: [],
    },
    mutations: {
        INIT_OBSERVATIONS(state) {
            state.observations = []
            state.observations = state.inat_data.map((data) => Object.values(data.observations)).flat()
        },
        INIT_USERS(state) {
            initializeData(state, 'users', 'users');
        },
        INIT_TAXA(state){
            initializeData(state, 'taxa', 'taxa');
        }

    },
    actions: {
        initData({ state }) {
            // this.commit("INIT_OBSERVATIONS")
            this.commit("INIT_USERS")
            this.commit("INIT_TAXA")

        }
    },
    getters: {
    }
})


function initializeData(state, dataKey, targetArray) {
    const startTime = new Date().getTime();

    

    state[targetArray] = [];
  
    const allData = Array.prototype.concat(...state.inat_data.map(data => Object.values(data[dataKey])));
  
    const uniqueData = Array.from(d3.group(allData, data => data.id).values(), values => values[0]);
  
    state[targetArray].push(...uniqueData);
  
    console.log(dataKey, allData.length, state[targetArray].length);
    const endTime = new Date().getTime();

    const executionTime = endTime - startTime;
    console.log(`Execution time ${dataKey}: ${executionTime} milliseconds`);
  }

export default store