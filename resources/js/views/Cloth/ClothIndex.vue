<template>
  <div>
    <v-card
      outlined
      class="ma-9 text-center"
      v-if="clothes.length === 0"
    >
      <v-card-title class="d-block text-center">
        There are no clothes available.
      </v-card-title>
      <v-card-text>
        Please insert cloth to continue.
      </v-card-text>
      <v-btn
        color="primary"
        class="ma-2"
        :to="{ name: 'clothes.create' }"
      >
        Insert
      </v-btn>
    </v-card>
    <p v-else v-for="cloth of clothes" v-bind:key="cloth.id">{{ cloth.name }}</p>
  </div>
</template>

<script>
export default {
  data(){
    return{
      clothes: []
    }
  },
  methods: {
    getClothes(){
      this.axios.get('/api/clothes')
      .then((response) => {
        console.log(response);
        this.clothes = response['data']['data'];
      })
      .catch((error) => {
        console.log(error);
      });
    }
  },

  created(){
    this.getClothes();
  }
}
</script>