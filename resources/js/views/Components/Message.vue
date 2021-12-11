<template>

  <v-alert
    :type="type"
    border="top"
    colored-border
    prominent
    elevation="2"
  >

    <h3>
      {{ title }}
    </h3>

    <div v-html="body"></div>
    
  </v-alert>

</template>

<script>
export default {
  props: {
    body: String,
    title: String,
    type: String
  }
};
</script>