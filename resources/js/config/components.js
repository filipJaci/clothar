import Vue from 'vue';

// importing

// messages
import Message from "../views/Components/Message";

// buttons
import ButtonCreate from '../views/Components/Buttons/ButtonCreate';
import ButtonBack from "../views/Components/Buttons/ButtonBack";

// registering
Vue.component('Message', Message);

Vue.component('ButtonCreate', ButtonCreate);
Vue.component('ButtonBack', ButtonBack);