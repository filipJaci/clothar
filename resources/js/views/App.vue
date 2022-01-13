<template>
  <v-app>
    <Navigation></Navigation>
    <v-main>
      <router-view
        :clothes="clothes"
        :days="days"
      />
      <ButtonBack/>
    </v-main>
  </v-app>
</template>

<script>

import Navigation from "./Components/Navigation";

export default {
  components: {
    Navigation
  },

  data() {
    return {
      clothes: [],
      days: [],
    };
  },

  methods: {
    // gets clothes from DB
    getClothes() {
      this.axios.get("/clothes")
      .then((response) => {
        // set clothes array
        this.setClothes(response["data"]);
      })
      .catch((error) => {});
    },

    // sets clothes array
    setClothes(clothes){
      this.clothes = clothes;
    },

    // gets days from the DB
    getDays() {
      this.axios.get("/days")
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
  },

  created() {
    // get clothes from the DB
    this.getClothes();
    // get days from the DB
    this.getDays();

    // bus methods
    EventBus.$on("getClothes", this.getClothes);
    EventBus.$on("setDays", this.setDays);
    EventBus.$on("getDays", this.getDays);
  },
};
</script>