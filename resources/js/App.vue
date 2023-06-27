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
</style>
<template>
    <Map />
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
        created() {
            // store.dispatch("initData")
        },
    })
</script>