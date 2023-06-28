<style>
    html, body, #app {
        height: 100%;
        width: 100%;
        margin: 0;
        padding: 0;
        font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
    }
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

	.switcher{
        display: flex;
		flex-wrap: wrap;
        justify-content: center;
        align-items: center;
		margin: .25rem;
	}
	.switcher .btn{
		font-size: 0.9rem;
		cursor: pointer;
		border-radius: 0 0 0 0;
		border: 1px solid #aaa;
		padding: .25rem 1rem;
		
	}
	.switcher .btn:first-child{
		border-radius: 1rem  0 0 1rem;
	}
	.switcher .btn:last-child{
		border-radius: 0 1rem 1rem 0;
	}
	.switcher .btn.selected{
		background: green;
		color:white;
	}
    @media screen and (max-width: 800px) {
		.switcher .btn{
			font-size:.7rem;
			padding: .125rem 0.5rem;
		}
	}
</style>
<template>
    <div class="switcher switcher-sm">
        <button
            class="btn"
            v-for="p in pages"
            :key="p"
            :class="{'selected': p === selected_page}"
            @click="selected_page = p"
            v-text="p"
        />
    </div>
    <nmw-table v-if="selected_page == 'NMW'"/>
    <Map v-else-if="selected_page == 'Map'" :key="map_key"/>
    <species-sunburst v-else-if="selected_page == 'taxa'"/>
</template>

<script lang="ts">
    import { defineComponent } from 'vue'
    import {mapState} from 'vuex'
    import store from './store'
    import Map from './components/Map.vue'
    import NmwTable from './components/NmwTable.vue'
    import SpeciesSunburst from './components/SpeciesSunburst.vue'

    export default defineComponent({
        name: "App",
        components: {
            Map, 
            NmwTable,
            SpeciesSunburst
        },
        data() {
            return {
                pages: ["NMW", "Map"],
                selected_page: "Map",
                map_key: 1,
            }
        },
        computed:{
        },
        watch:{
            selected_page(){
                this.map_key++
            }
        },
        created() {
            store.dispatch("initData")            
        },
        methods:{
            
        }
    })
</script>