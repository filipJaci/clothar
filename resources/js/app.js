require('./bootstrap');
import Vue from 'vue';

// app component
import App from './views/App.vue';

// vue router and axios
import VueRouter from 'vue-router';
import VueAxios from 'vue-axios';
import axios from 'axios';
import {routes} from './config/routes';

// vuetify
import vuetify from './config/vuetify';

// vee-validate
import {
    ValidationProvider,
    ValidationObserver
} from 'vee-validate/dist/vee-validate.full';



window.Vue = Vue;

Vue.use(VueRouter);
Vue.use(VueAxios, axios);

Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);

const router = new VueRouter({
    mode: 'history',
    routes: routes,
    props: true
});

const app = new Vue({
    el: '#app',
    router: router,
    vuetify,
    render: h => h(App),
});