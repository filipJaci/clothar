export default {
  namespaced: true,
  // data
  state:{
    // array of Clothes
    clothes: [],
  },

  // getters
  getters:{
    // gets Clothes
    clothes(state){
      // return Clothes
      return state.clothes;
    }
  },

  // mutations
  mutations:{
    // sets Clothes
    SET_CLOTHES (state, value) {
      // set Clothes to assigned value
      state.clothes = value;
    }
  },

  // methods
  actions:{
    // saves Clothes
    saveClothes({commit}, data){
      commit('SET_CLOTHES', data);
    },
    // removes Clothes
    removeClothes({commit}){
      commit('SET_CLOTHES', []);
    }
  }
}