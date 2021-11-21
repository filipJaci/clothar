<template>

  <div v-if=" !cloth ">

    <v-dialog
      v-if=" errorMessage === '' "
      v-model="dialog"
      hide-overlay
      persistent
      width="300"
    >

      <v-card
        color="primary"
        dark
      >

        <v-card-text>
          Please stand by

          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>

        </v-card-text>

      </v-card>

    </v-dialog>

    <v-alert 
      v-else
      border="top"
      colored-border
      type="error"
      elevation="2"
      class="mt-2"
    >
      {{ errorMessage }}
    </v-alert>

  </div>

  <ClothForm v-else :cloth = cloth></ClothForm>

</template>

<script>

import ClothForm from './ClothForm.vue'

export default {

  components: {
    ClothForm
  },

  data() {
    return {
      cloth: null,
      errorMessage: '',
      dialog: false
    }
  },

  methods: {

    getCloth(){

      let id = this.$route.params.id;
      
      this.axios.get('/api/clothes/' + id)
      .then((response) => {
        this.cloth = response['data']['data'];
      })
      .catch((error) => {
        if(error.response.status === 404){
          this.errorMessage = 'Invalid Cloth ID, this item may have been deleted previously.'
        }
        else{
          this.errorMessage = 'Unkown error occured while trying to retreat Cloth info, please try again later.'
        }
      });
      
    }
  },

  mounted(){
    this.getCloth();
  }

}
</script>

<style>

</style>