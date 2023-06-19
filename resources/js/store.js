import { createStore } from 'vuex'
import * as d3 from 'd3'
import moment from 'moment'
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
            let startDate = new Date("2010-01-01");
            let endDate = new Date('2011-12-01')
            let slices = [];
            let pullFlag = true
            let count = 0;
            while (pullFlag) {
                const data = await getInatObservations(startDate, endDate);
                const totalObservations = data.total_results
            
                if (totalObservations < 7500){
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
                }
                count++;
                if(endDate > new Date() || slices.length > 100){
                    pullFlag = false
                }
                setTimeout(() => {
                    console.log(1)
                  }, 60000)
            }
            console.table(slices)
            console.log(startDate, endDate, slices)
        },
        async INIT_APIx(state) {
            let totalObservations = 0;
            let pullSegments = [];

            let today = new Date();
            // let startDate = new Date('2000-01-01');
            // let endDate = new Date('2011-10-01');
            let startDate = new Date('2020-05-01');
            let endDate = new Date('2020-11-01');
            let pull_flag = true;
            let iteration_count = 0;

            while (pull_flag) {
                const params = {
                    place_id: 6681,
                    taxon_id: 47157,
                    per_page: 1,
                    d1: formatDate(startDate),
                    d2: formatDate(endDate),
                };

                const data = await inatPull(params);
                const totalResults = data.total_results;

                if (totalResults < 2500 && iteration_count < 5) {
                    console.log("+", totalResults, totalObservations)
                    endDate.setMonth(endDate.getMonth() + 1);
                    iteration_count++;
                } else if(totalResults >= 9500){
                    console.log("-", totalResults, totalObservations, formatDate(startDate), formatDate(endDate))
                    if(endDate.getMonth() - startDate.getMonth() > 2){
                        endDate.setMonth(endDate.getMonth() -1);
                    } else {
                        let midDate = (endDate.getTime() + startDate.getTime())/ (1000 * 60 * 60 * 24 * 2)
                        console.log(midDate)
                        endDate.setDate(midDate-1);
                    }
                    iteration_count++
                } else {
                    console.log(params, totalResults, totalObservations)
                    pullSegments.push({
                        start_date: formatDate(startDate),
                        end_date: formatDate(endDate),
                        observations: totalResults,
                    });
                    totalObservations += totalResults;
                    startDate = new Date(formatDate(endDate))
                    endDate.setMonth(endDate.getMonth() + 6);
                    iteration_count = 0
                }
                if(endDate > today || totalResults == 0 || totalObservations >= 100000){
                    console.log(endDate > today, totalResults == 0, totalObservations >= 100000, iteration_count)
                    pull_flag = false;
                }
            }
            state.urls = pullSegments
            console.table(state.urls)
            // return pullSegments;
        }
    },
    actions: {
        initData({ state }) {
            // this.commit("INIT_OBSERVATIONS")
            // this.commit("INIT_USERS")
            // this.commit("INIT_TAXA")
            this.commit("INIT_API")

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
    const url = 'https://api.inaturalist.org/v1/observations';
    return axios.get(url, { params }).then(response => response.data);
}

function getInatObservations(startDate, endDate) {
    // console.log("pull", formatDate(startDate), formatDate(endDate))
    const params = {
        place_id: 6681,
        taxon_id: 47157,
        per_page: 1,
        d1: formatDate(startDate),
        d2: formatDate(endDate),
    };
    setTimeout(() => {
        console.log(1)
      }, 60000)
    return axios.get('https://api.inaturalist.org/v1/observations', { params }).then(response => response.data);
}

function getObservationsPerDay(start, end, observations){
    let total_days = (end.getTime() - start.getTime()) / (1000 * 60 * 60 * 24)
    return observations / total_days
}

async function updateEndDate(type, start, end, observations){
    let op = end
    let difference = moment(end).diff(moment(start), 'day')
    if(type == "+"){
        if(difference > 50){
            op = moment(end).add(1, 'month')
        } else {
            op = moment(end).add(8, 'day')
        }
    } else if (type == "-"){
        if(difference > 50){
            op = moment(end).subtract(1, 'month')
        } else {
            op = moment(start).subtract(Math.ceil(difference/2), 'day')
        }
    } else if(type == "="){
        let obs_per_day = getObservationsPerDay(start, end, observations)
        let days = Math.ceil(9000/obs_per_day)
        op = moment(end).add(days, 'day')
    }
    return new Date(op.format("YYYY-MM-DD"))
}


export default store
