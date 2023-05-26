<template>
    <div>
        Store data 
    </div>
</template>

<script lang="ts">
    import { defineComponent } from 'vue'
    import {mapState} from 'vuex'
    import store from './store'

    export default defineComponent({
        name: "App",
        computed:{
            ...mapState(["inat_data"])
        },
        watch:{
            inat_data:{
                handler(){
                    console.log("this.inat_data")
                },
                deep:true
            }
        },
        mounted() {
            // console.log(this.inat_data)
            store.dispatch("initData")
        },
    })
</script>