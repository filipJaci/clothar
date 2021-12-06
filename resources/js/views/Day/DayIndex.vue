<template>
  <div class="text-center my-3">

    <div
      v-if="false"
    >
        <v-btn
          color="success"
          @click="wearClothes"
        >
          Wear Clothes
        </v-btn>
    </div>

    <div
      v-else
    >
      <v-alert
        border="top"
        colored-border
        type="info"
        elevation="2"
      >
        There are no clothes in the Database, please insert a cloth to continue.
      </v-alert>

      <v-btn
        color="success"
        class="text-decoration-none"
        :to="{ name: 'clothes.create' }"
        x-large
      >
        Add New Cloth
      </v-btn>

    </div>

  </div>

</template>

<script>
  export default {

    props: { clothes: Array },

    data () {
      return {
        alert: true,
      }
    },

    methods: {

      wearClothes(){

        console.log(1);

      }
      
    }
  }
</script>