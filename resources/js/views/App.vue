<template>
  <v-app>
    <Navigation v-if="isLoggedIn"></Navigation>
    <v-main>
      <router-view
        :clothes="clothes"
        :days="days"
      />
      <ButtonBack/>
      <p v-if="!isLoggedIn">is not logged in</p>
    </v-main>
  </v-app>
</template>

<script>
import { mapActions } from 'vuex';
import Navigation from "./Components/Navigation";

export default {

  name: 'App',

  components: {
    Navigation
  },

  data() {
    return {
      clothes: [],
      days: [],
      isLoggedIn: false,
    };
  },

  methods: {
    // registers a Vuex method
    ...mapActions({
      // saveClothes refers to saveClothes method found in clothes
      saveClothes: 'clothes/saveClothes'
    }),

    // gets clothes from DB
    getClothes() {
      return this.axios.get("/clothes")
      .then((response) => {
        // set clothes array
        this.saveClothes(response["data"]);
      })
      .catch((error) => {});
    },

    // gets days from the DB
    getDays(){
      return this.axios.get("/days")
      .then((response) => {
        // set days array
        this.setDays(response["data"]);
      })
      .catch((error) => {
        console.log(error);
      });
    },

    // sets days array
    setDays(days){
      // sets days array
      this.days = days;
      // run setWorn bus method on DayIndex
      EventBus.$emit('setWorn', days);
    },

    

    // handles login
    handleLogin(){
      // is the user logged in
      if (this.$store.state.auth.authenticated) {
        // set user as logged in
        this.isLoggedIn = true;
      }
    },

    // handles logout
    handleLogout(){
      // set user as logged out
      this.isLoggedIn = false;
    },
    
  },

  created() {
    // handle login
    this.handleLogin();

    // bus methods
    EventBus.$on("setClothes", this.setClothes);
    EventBus.$on("getDays", this.getDays);
    EventBus.$on("handleLogin", this.handleLogin);
    EventBus.$on("handleLogout", this.handleLogout);
  },
};
</script>