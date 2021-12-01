<template>
  <v-app>
      <Navigation></Navigation>
      <v-main>
          <router-view :clothes = clothes></router-view>
      </v-main>
  </v-app>
</template>

<script>

import Navigation from './Navigation';

export default {

    components: {
        Navigation,
    },

    data(){
        return{
            clothes: [],
        }
    },

    methods: {

        getClothes(){

            this.axios.get('/clothes')
            .then((response) => {
                this.clothes = response['data'];
            })
            .catch((error) => {
                console.log(error);
            });

        }

    },

    beforeMount(){
        this.getClothes();
    }
}
</script>