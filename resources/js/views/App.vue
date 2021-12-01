<template>
  <v-app>
      <Navigation></Navigation>
      <v-main>
          <router-view :clothes = clothes></router-view>
      </v-main>
      <Back></Back>
  </v-app>
</template>

<script>

import Navigation from './Navigation';
import Back from './Back';

export default {

    components: {
        Navigation,
        Back,
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