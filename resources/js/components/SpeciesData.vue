<style>
.table td,
.table th {
    text-align: center;
}
</style>

<template>
    <div class="container-fluid px-3 py-2">
        <table class="table table-sm">
            <thead class="table-dark">
                <tr>
                    <th>Species</th>
                    <th v-for="i in years" :key="i" v-text="i"></th>
                    <th>Total Observations</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="species in species_list"
                    :key="species.scientific_name"
                >
                    <td v-text="species.scientific_name" />
                    <td
                        v-for="i in years"
                        :key="i"
                        v-text="species[i]"
                        :class="species[i] ? 'bg-success' : ''"
                    />
                    <td v-text="species.total_observations" />
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script lang="js" setup>
import { onMounted, computed } from "vue";
import { useStore } from "vuex";

const store = useStore();

const geojson = computed(() => store.state.geojson);
const nmw_data = computed(() => store.state.nmw_data);

const years = [2012,2013,2014,2015,2016,2017,2018,2019,2020,2021,2022,2023]

const species_list = computed(() => {
    let year_data = [];
    let complete_list = [];
    let op = [];
    Object.keys(nmw_data.value).forEach((year) => {
        const {total_observations, total_taxa, total_users, ...others} = nmw_data.value[year]
        const species = others.count_id_levels.species.species
        complete_list.push(...species.map((s) => s.scientific_name))
        year_data.push({
            year: year,
            species: species,
        })
    })
    const unique_species_list = [...new Set(complete_list)].sort()
    unique_species_list.forEach((species) => {
        let row = {
            scientific_name: species,
            total_observations: 0
        }
        for(let i=2012 ; i<2024 ; i++ ){
            let species_found = nmw_data.value[i].count_id_levels.species.species.filter((s) => s.scientific_name == species)
            if(species_found.length == 1){
                row[i] = species_found[0].observations
                row.total_observations += species_found[0].observations
            }
            // row[i] =
            // console.log()
        }
        op.push(row)
    })





    return op;
})
</script>
