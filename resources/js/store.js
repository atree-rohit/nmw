import { createStore } from 'vuex'
import * as d3 from 'd3'
import moment from 'moment'
import inat_pull_list from './data/inat_pull_list_1.json'
import districts from './geojson/districts.json'
import states from './geojson/states.json'
import regions from './geojson/regions.json'
import data_1 from './inat_data/to_2020_01_01.json'
import data_2 from './inat_data/2020_01_01_to_2021_06_01.json'
import data_3 from './inat_data/2021_06_01_to_2022_06_01.json'

import axios from 'axios'

const store = createStore({
    state: {
        // geojson: {
        //     districts: districts,
        //     states: states,
        //     regions: regions
        // },
        // inat_data: [data_1, data_2, data_3],
        observations: [],
        users: [],
        taxa: [],
        urls: [],
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
        },
        async INIT_API(state) {
            let startDate = new Date("2023-02-06")
            let endDate = new Date("2023-02-16")
            let slices = [];
            let pullFlag = true
            let count = 0;
            let last_pull_time = new Date()
            while (pullFlag) {
                let time_since_last_pull = moment(new Date()).diff(last_pull_time, 'second')
                // do{
                //     time_since_last_pull = moment(new Date()).diff(last_pull_time, 'second')
                //     setTimeout(() => {
                //     }, 1500)
                //     console.log("wait")
                    
                    
                // } while(time_since_last_pull < 15)
                const data = await getInatObservations(startDate, endDate)
                last_pull_time = new Date()
                const totalObservations = data.total_results
            
                if (totalObservations < 2500){
                    endDate = await updateEndDate("+", startDate, endDate, totalObservations)
                    console.log("<", formatDate(endDate), totalObservations)
                } else if(totalObservations >= 10000){
                    endDate = await updateEndDate("-", startDate, endDate, totalObservations)
                    console.log(">", formatDate(endDate), totalObservations)
                } else {
                    const slice = {
                        startDate: formatDate(startDate),
                        endDate: formatDate(endDate),
                        totalObservations: totalObservations
                    };
                    slices.push(slice);
                    startDate = new Date(endDate);
                    endDate = await updateEndDate("=", startDate, endDate, totalObservations)
                    console.log("=", formatDate(endDate))
                    console.log(slices)
                }
                count++;
                if(endDate > new Date() || slices.length > 25 || startDate > endDate){
                    pullFlag = false
                }
                // pullFlag = false
            }
            console.table(slices)
            console.log(slices)
            console.log(startDate, endDate, slices)
        },
        async PULL_INAT_DATA(state) {
            // console.log(inat_pull_list)
            let inat_data = await inatPull({...inat_pull_list[0], page: 1})
            axios.post("/add_data", inat_data)
            // console.log(inat_data.results[0])
        }
    },
    actions: {
        initData({ state }) {
            // this.commit("INIT_OBSERVATIONS")
            // this.commit("INIT_USERS")
            // this.commit("INIT_TAXA")
            // this.commit("INIT_API")
            this.commit("PULL_INAT_DATA")

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

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function inatPull(params) {
    let url = 'https://api.inaturalist.org/v1/observations?'
    let parameters = {
        place_id: 6681,
        taxon_id: 47157,
        per_page: 10,
        page: params.page,
        d1: params.startDate,
        d2: params.endDate,
    }
    let pars = []
    Object.keys(parameters).forEach(key => pars.push(`${key}=${parameters[key]}`))
    url = url + pars.join('&')
    return axios.get(url).then(response => response.data)
}

function getInatObservations(startDate, endDate) {
    console.log("pull", formatDate(startDate), formatDate(endDate))
    const params = {
        place_id: 6681,
        taxon_id: 47157,
        per_page: 1,
        d1: formatDate(startDate),
        d2: formatDate(endDate),
    };
    
    return axios.get('https://api.inaturalist.org/v1/observations', { params }).then(response => response.data);
}

function getObservationsPerDay(start, end, observations){
    let total_days = moment(end).diff(moment(start), 'day')
    return observations / total_days
}

async function updateEndDate(type, start, end, observations){
    let op = end
    let difference = moment(end).diff(moment(start), 'day')
    let obv_per_day = getObservationsPerDay(start, end, observations)
    if(type == "+"){
        // if(observations < 100){
        //     op = moment(end).add(3, 'month')
        // } else if (observations < 1000){
        //     op = moment(end).add(2, 'month')
        // } else if (observations < 5000){
        //     op = moment(end).add(1, 'month')
        // } else {
        //     op = moment(end).add(1, 'day')
        // }
        op = moment(end).add(7, 'day')
    } else if (type == "-"){
        if(difference > 50){
            op = moment(end).subtract(1, 'month')
        } else if(difference > 25) {
            op = moment(start).subtract(15, 'day')
        } else {
            op = moment(start).subtract(1, 'day')
        }
    } else if(type == "="){
        console.log(55, start, end, observations)
        let obs_per_day = getObservationsPerDay(start, end, observations)
        let days = Math.ceil(9000/obs_per_day)
        op = moment(end).add(1, 'day')
    }
    return new Date(op.format("YYYY-MM-DD"))
}


export default store
