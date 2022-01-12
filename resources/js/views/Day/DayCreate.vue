<template>

  <div class="text-center">

    <ButtonCreate
      key="clothes.create"
      text="Wear a Cloth"
      origin="DayIndex"
      @click.native="dialog = !dialog"
    />

    <v-dialog
      v-model="dialog"
      class="overflow-visible"
    > 

      <DayForm
        :clothes = clothes
        :alreadyWorn = []
      />
      
    </v-dialog>
  </div>
</template>

<script>

import DayForm from './DayForm';

  export default {
    
    components: { DayForm },

    props: { 
      date: String,
      clothes: Array
    },

    data () {
      return {
        dialog: false,
      }
    },
  }
</script>