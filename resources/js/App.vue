<template>
    <div>
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
            
        },
        watch:{
        },
        mounted() {
            store.dispatch("initData")
        },
    })
</script>