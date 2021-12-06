<template>

  <div class="text-center mb-3">

    <v-btn
      color="success"
      class="text-decoration-none"
      :to="link"
      x-large
    >
      {{ text }}
    </v-btn>

  </div>

</template>

<script>
export default {

  props: {
    text: String,
    link: Object
  }
  
}
</script>

<style>

</style>