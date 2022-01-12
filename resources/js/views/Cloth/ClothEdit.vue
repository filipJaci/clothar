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

  <!-- modal -->
  <v-dialog
    v-else
    v-model="dialog"
    class="overflow-visible"
    max-width="600px"
  > 
    <!-- form for createing cloth -->
    <ClothForm
      :cloth = cloth
    />
  </v-dialog>

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

      this.axios.get('/clothes/' + id)
      .then((response) => {
        this.cloth = response['data'];
      })
      .catch((error) => {});
      
    }
  },

  mounted(){
    this.getCloth();
  }

}
</script>

<style>

</style>