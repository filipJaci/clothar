import Vue from 'vue';
import Vuex from 'vuex';
import createPersistedState from 'vuex-persistedstate';

import auth from './auth';
import clothes from './clothes';
import days from './days';

Vue.use(Vuex);

export default new Vuex.Store({
  plugins:[
    createPersistedState()
  ],
  modules:{
    auth,
    clothes,
    days
  }
})