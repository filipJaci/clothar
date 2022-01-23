// import axios from 'axios';
// import { cond } from 'lodash';
import router from '../config/router'

export default {
  namespaced: true,

  state:{
    authenticated:false,
    token:''
  },

  getters:{
    authenticated(state){
      return state.authenticated
    },

    token(state){
      return state.token
    }
  },

  mutations:{
    SET_AUTHENTICATED (state, value) {
      state.authenticated = value
    },
    SET_TOKEN (state, value) {
      state.token = value
    }
  },

  actions:{
    login({commit}, data){
      commit('SET_TOKEN',data['token'])
      commit('SET_AUTHENTICATED',true)
      router.push({name:'home'});
    },

    logout({commit}){
      commit('SET_TOKEN','')
      commit('SET_AUTHENTICATED',false)
    }
  }
}