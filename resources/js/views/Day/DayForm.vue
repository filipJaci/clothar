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
export default {

  props: {
    // all clothes in the DB
    clothes: Array,
    // day info
    currentDayInformation: Object,
  },

  data(){
    return{
      // modal title
      modalTitle: '',
      // clothes that were worn on a day
      worn: [],
    }
  },

  computed:{
    // toggles if button submit should be enabled or not, if there were new changes
    buttonSubmit(){
      // number of clothes between previously worn and currently worn doesn't match up
      return this.currentDayInformation.worn.length !== this.worn.length || 
      // or
      // JSON stringified previously worn and currently worn don't match
      JSON.stringify( this.currentDayInformation.worn.sort() ) !== 
      JSON.stringify( this.worn.sort() );
    },
  },

  methods: {
    // sets modal date
    setModalTitle(){
      this.modalTitle = "Clothes on:<br>"+this.moment(this.currentDayInformation.date).format('dddd, MMMM Do YYYY');
    },

    // sets clothes worn on a date
    setWorn(){
      // pushes id of every cloth item worn on a date
      this.currentDayInformation.worn.forEach(cloth_id => {
        this.worn.push(cloth_id)
      });
    },

    // handles click on a cloth
    handleCloth(cloth_id){
      // cloth has been previously selected as worn
      if( this.worn.includes(cloth_id) ){
        // removes cloth from worn
        this.worn.splice(this.worn.indexOf(cloth_id),1);
      }

      // cloth has not been previously selected as worn
      else{
        // adds cloth as worn
        this.worn.push(cloth_id);
      }
    },

    // handles click on the enabled Submit button
    handleSubmit(){
      // there are no clothes being worn
      if (this.worn.length === 0){
        // delete day from the DB
        this.dayDestroy();
      }
      // there are clothes being worn
      else{
        // store day data to the DB
        this.dayStore();
      }

    },

    // deletes day from the DB
    dayDestroy(){
      this.axios.delete('/days/' + this.currentDayInformation.id)
      .then((response) => {
        // run setDays bus method on DayIndex
        EventBus.$emit('setDays', response['data']);
        // run closeViewDay bus method on DayIndex
        EventBus.$emit('closeViewDay');
      })
      .catch((error) => {});
    },

    // store day data to the DB
    dayStore(){
      
      this.axios.post('/days', {
        date: this.currentDayInformation.backEndDate,
        clothes: this.worn,
      })
      .then((response) => {
        // run setDays bus method on DayIndex
        EventBus.$emit('setDays', response['data']);
        // run closeViewDay bus method on DayIndex
        EventBus.$emit('closeViewDay');
      })
      .catch((error) => {});

    },

    clear(){
      // resets selected clothes
      this.worn = [];

      // for(let value in this.day){
      //   this.day[value] = '';
      // }
      
    },



    dayUpdate(){



    },

  },

  beforeMount(){
    // set modal title
    this.setModalTitle();
    // set worn clothes
    this.setWorn();
  },

  beforeDestroy(){
    // empties worn
    this.worn = [];
  },
}
</script>

<style>

</style>