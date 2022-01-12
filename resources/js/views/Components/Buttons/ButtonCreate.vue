<template>
  <div class="text-center">
      <v-btn
        color="success"
        class="text-decoration-none"
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
  }
  
}
</script>

<style>

</style>