<style scoped>
    th, td{
        text-align: center;
        padding: .5rem;
    }
    .nmw-table-container{
        padding: 0 1rem;
        max-width: 100vw;
        overflow: auto;
    }
    @media screen and (max-width: 800px) {
		.nmw-table-container{
			padding: 0 .25rem;
		}
        
	}
</style>

<template>
    <div class="nmw-table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Observations</th>
                    <th>States</th>
                    <th>Districts</th>
                    <th>Locations</th>
                    <th>Unique Taxa</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in nmw"
                    :key="row.year"
                >
                    <td v-text="row.year" />
                    <td v-text="row.observations" />
                    <td v-text="row.states" />
                    <td v-text="row.districts" />
                    <td v-text="row.locations" />
                    <td v-text="row.unique_taxa" />
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import {mapState} from 'vuex'
export default defineComponent({
    name:"NmwTable",
    data(){
        return{
        }
    },
    computed: {
        ...mapState(["nmw"])
    },
})
</script>