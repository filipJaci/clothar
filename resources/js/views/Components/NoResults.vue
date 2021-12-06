<template>

  <v-card
    outlined
    class="ma-9 text-center"
  >

    <v-card-title class="d-block text-center">
      {{ title }}
    </v-card-title>

    <v-card-text>
      {{ body }}
    </v-card-text>
  
  </v-card>

</template>


<script>
    export default {

        props: {

            title: String,
            body: String,
            linkToAddNew: Object,

        },

    }
</script>