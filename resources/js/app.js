require('./bootstrap');
import Vue from 'vue';

// app component
import App from './views/App.vue';

// vue router and axios
import VueRouter from 'vue-router';
import VueAxios from 'vue-axios';
import axios from 'axios';

import {routes} from './config/routes';
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



window.Vue = Vue;

Vue.use(VueRouter);
Vue.use(VueAxios, axios);
Vue.prototype.moment = moment;
Vue.use(VueSweetalert2, sweetAlert2);

Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);

const router = new VueRouter({
    mode: 'history',
    routes: routes,
    props: true
});

// Event Bus
window.EventBus = new Vue();

const app = new Vue({
    el: '#app',
    router: router,
    vuetify,
    render: h => h(App),
});

