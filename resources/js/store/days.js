export default {
  namespaced: true,
  // data
  state:{
    // array of Days
    days: [],
  },

  // getters
  getters:{
    // gets days
    days(state){
      // return days
      return state.days;
    }
  },

  // mutations
  mutations:{
    // sets Days
    SET_DAYS (state, value) {
      // set Days to assigned value
      state.days = value;
    }
  },

  // methods
  actions:{
    // saves Days
    saveDays({commit}, data){
      commit('SET_DAYS', data);
    },
    // removes Days
    removeDays({commit}){
      commit('SET_DAYS', []);
    }
  }
}