export default {
  namespaced: true,
  // Data.
  state:{
    // Array of Days.
    days: [],
  },

  // Getters.
  getters:{
    // Gets Days.
    days(state){
      // Return Days.
      return state.days;
    }
  },

  // Mutations.
  mutations:{
    // Sets Days.
    SET_DAYS (state, value) {
      // Set Days to assigned value.
      state.days = value;
    }
  },

  // Methods.
  actions:{
    // Saves Days.
    saveDays({commit}, data){
      commit('SET_DAYS', data);
    },
    // Removes Days.
    removeDays({commit}){
      commit('SET_DAYS', []);
    }
  }
}