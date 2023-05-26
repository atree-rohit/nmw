<template>
    <div>
        Store data {{ test }}
    </div>
</template>

<script lang="ts">
    import { defineComponent } from 'vue'
    import {mapState} from 'vuex'

    export default defineComponent({
        name: "App",
        computed:{
            ...mapState(["test"])
        },
        mounted() {
            console.log('App mounted')
        },
    })
</script>