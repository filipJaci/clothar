<template>

  <v-card>

    <v-card-title><span v-html="modalTitle"></span></v-card-title>

    <v-card-text>

        <v-chip
          v-for="cloth in clothes"
          :key="cloth.id"
          :color=" worn.includes(cloth.id) ? 'success' : '' "
          class="mx-1"
          @click="handleCloth(cloth.id)"
        >
          {{ cloth.title }}
        </v-chip>
    
    </v-card-text>

    <v-card-actions
      class="d-flex justify-center"
    >
      
      <v-btn
        color="success"
        type="submit"
        :disabled="!buttonSubmit"
        x-large
        @click="handleSubmit()"
      >
        Submit
      </v-btn>

      <v-btn
        @click="clear"
        color="warning"
        x-large
      >
        Clear
      </v-btn>

    </v-card-actions>       

  </v-card>

</template>

<script>
import { mapActions } from 'vuex'

export default {

  props: {
    // All clothes in the DB.
    clothes: Array,
    // Day info.
    currentDayInformation: Object,
  },

  data(){
    return{
      // Modal title.
      modalTitle: '',
      // Clothes that were worn on a day.
      worn: [],
    }
  },

  computed:{
    // Toggles if button submit should be enabled or not, if there were new changes.
    buttonSubmit(){
      // Number of clothes between previously worn and currently worn doesn't match up
      return this.currentDayInformation.worn.length !== this.worn.length || 
      // or
      // JSON stringified previously worn and currently worn don't match.
      JSON.stringify( this.currentDayInformation.worn.sort() ) !== 
      JSON.stringify( this.worn.sort() );
    },
  },

  methods: {
    // Registers a Vuex method.
    ...mapActions({
      // saveDays refers to saveDays method found in days.
      saveDays: 'days/saveDays'
    }),

    // Sets modal Date.
    setModalTitle(){
      this.modalTitle = "Clothes on:<br>"+this.moment(this.currentDayInformation.date).format('dddd, MMMM Do YYYY');
    },

    // Sets Clothes worn on a Date.
    setWorn(){
      // Pushes id of every cloth item worn on a date.
      this.currentDayInformation.worn.forEach(cloth_id => {
        this.worn.push(cloth_id);
      });
    },

    // Handles click on a Cloth.
    handleCloth(cloth_id){
      // Cloth has been previously selected as worn.
      if( this.worn.includes(cloth_id) ){
        // Removes cloth from worn.
        this.worn.splice(this.worn.indexOf(cloth_id),1);
      }

      // cloth has not been previously selected as worn
      else{
        // adds cloth as worn
        this.worn.push(cloth_id);
      }
    },

    // Handles click on the enabled Submit button.
    handleSubmit(){
      // There are no clothes being worn.
      if (this.worn.length === 0){
        // delete day from the DB
        this.dayDestroy();
      }
      // There are clothes being worn.
      else{
        // There is Day data already in the DB.
        if(Boolean(this.currentDayInformation.id)){
          // Update day data to the DB.
          this.dayUpdate();
        }
        // There is no Day data already in the DB.
        else{
          // Store day data to the DB.
          this.dayStore();
        }

      }

    },

    // Deletes day from the DB.
    dayDestroy(){
      this.axios.delete('/days/' +  this.currentDayInformation.id)
      .then((response) => {
        // Run Vuex method saveDays method.
        this.saveDays(response['data']);
        // Run handleDaySave bus method on DayIndex.
        EventBus.$emit('handleDaySave');
      })
      .catch((error) => {});
    },

    // Stroes day data to the DB.
    dayStore(){
      
      this.axios.post('/days', {
        date: this.currentDayInformation.backEndDate,
        clothes: this.worn,
      })
      .then((response) => {
        // Run Vuex method saveDays method.
        this.saveDays(response['data']);
        // Run handleDaySave bus method on DayIndex.
        EventBus.$emit('handleDaySave');
      })
      .catch((error) => {});

    },

    // Updates day data to the DB.
    dayUpdate(){
      this.axios.patch('/days', {
        id: this.currentDayInformation.id,
        clothes: this.worn,
      })
      .then((response) => {
        // Run Vuex method saveDays method.
        this.saveDays(response['data']);
        // Run handleDaySave bus method on DayIndex.
        EventBus.$emit('handleDaySave');
      })
      .catch((error) => {});

    },
    
    // Clears view Day form.
    clear(){
      // Reset worn array.
      this.worn = [];
    },

  },

  beforeMount(){
    // Set modal title.
    this.setModalTitle();
    // Set worn clothes.
    this.setWorn();
  },

  beforeDestroy(){
    // Empties worn.
    this.worn = [];
  },
}
</script>

<style>

</style>