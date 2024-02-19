// import './bootstrap'

import {createApp} from "vue"
import App from './App.vue'
import store from './store'
import 'bootstrap/dist/css/bootstrap.min.css';


let app = createApp(App)
app.use(store)
app.mount('#app')
