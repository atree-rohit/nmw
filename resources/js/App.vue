<template>
    <div>
        <!-- <pre>
            {{ expandedData }}
        </pre>
        <pre>
            {{ polygonData }}
        </pre> -->
        <Map />
    </div>
</template>

<script lang="ts">
    import { defineComponent } from 'vue'
    import {mapState} from 'vuex'
    import store from './store'
    import Map from './components/Map.vue'

    export default defineComponent({
        name: "App",
        components: {
            Map
        },
        computed:{
            polygonData() {
                return this.$store.getters.polygon_data
            },
            expandedData() {
                return this.$store.getters.expanded_data
            },
        },
        watch:{
        },
        created() {
            // store.dispatch("initData")
        },
    })
</script>