<template>
  <ClothForm :cloth = cloth></ClothForm>
</template>

<script>

import ClothForm from './ClothForm.vue'

export default {

  components: {
    ClothForm
  },

  data(){

    return{

      cloth: {
        title: '',
        description: '',
        buy_at: '',
        buy_date: null,
        category: 1,
        status: 1
      },

    }
  }
}
</script>