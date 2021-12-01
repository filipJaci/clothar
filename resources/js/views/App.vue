<template>
  <v-app>
      <Navigation></Navigation>
      <v-main>
          <router-view :clothes = clothes></router-view>
          <Back></Back>
      </v-main>
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

    created(){
        // Get clothes
        this.getClothes();

        // Bus methods
        EventBus.$on('getClothes', this.getClothes);
    }
    
}
</script>