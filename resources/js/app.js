require('./bootstrap');
import Vue from 'vue';

import App from './views/App.vue';
import VueRouter from 'vue-router';
import VueAxios from 'vue-axios';
import axios from 'axios';
import {routes} from './config/routes';
import vuetify from './config/vuetify';



window.Vue = Vue;

Vue.use(VueRouter);
Vue.use(VueAxios, axios);

const router = new VueRouter({
    mode: 'history',
    routes: routes
});

const app = new Vue({
    el: '#app',
    router: router,
    vuetify,
    render: h => h(App),
});