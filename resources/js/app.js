require('./bootstrap');
import Vue from 'vue';

// app component
import App from './views/App.vue';

// vue router and axios
import router from './config/router';
import VueAxios from 'vue-axios';
import axios from 'axios';
import './config/axios';

// vuetify
import vuetify from './config/vuetify';
// moment js
import moment from 'moment';

// sweet alert 2
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import sweetAlert2 from './config/sweetAlert2';

// vee-validate
import {
    ValidationProvider,
    ValidationObserver
} from 'vee-validate/dist/vee-validate.full';

// vuex
import store from './store'

window.Vue = Vue;

Vue.use(VueAxios, axios);
Vue.prototype.moment = moment;
Vue.use(VueSweetalert2, sweetAlert2);

Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);

// custom components
import './config/components';

// Event Bus
window.EventBus = new Vue();

const app = new Vue({
    el: '#app',
    router: router,
    vuetify,
    render: h => h(App),
    store:store
});

