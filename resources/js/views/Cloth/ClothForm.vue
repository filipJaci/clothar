<template>

  <validation-observer
    ref="observer"
    v-slot="{ invalid }"
  >
  
    <form @submit.prevent="submit">

      <v-card>

        <v-card-title>Create New Cloth</v-card-title>

        <v-card-text>

          <validation-provider
            v-slot="{ errors }"
            name="title"
            rules="required"
          >

            <v-text-field
              v-model="cloth.title"
              :error-messages="errors"
              label="Title"
            ></v-text-field>

          </validation-provider>

          <validation-provider
            v-slot="{ errors }"
            name="description"
          >

            <v-text-field
              v-model="cloth.description"
              :error-messages="errors"
              label="Description"
            ></v-text-field>

          </validation-provider>

          <validation-provider
            v-slot="{ errors }"
            name="buy_at"
          >

            <v-text-field
              v-model="cloth.buy_at"
              :error-messages="errors"
              label="Buy location"
            ></v-text-field>

          </validation-provider>

          <validation-provider
            name="buy_date"
            class="d-block text-center"
          >

            <v-dialog
              ref="dialog"
              v-model="modal"
              :return-value.sync="cloth.buy_date"
              persistent
              width="290px"
            >

              <template v-slot:activator="{ on, attrs }">

              <v-text-field
                :value="formatedDate"
                clearable
                label="Date cloth was bought on"
                readonly
                v-bind="attrs"
                v-on="on"
                @click:clear="cloth.buy_date = null"
              ></v-text-field>

              </template>

              <v-date-picker
                v-model="cloth.buy_date"
                scrollable
              >

                <v-spacer></v-spacer>

                <v-btn 
                  text
                  color="primary"
                  @click="$refs.dialog.save(null)"
                >
                  Clear
                </v-btn>

                <v-btn
                  text
                  color="primary"
                  @click="$refs.dialog.save(cloth.buy_date)"
                >
                  OK
                </v-btn>

              </v-date-picker>

            </v-dialog>
          
          </validation-provider>
        
        </v-card-text>

        <v-card-actions
          class="d-flex justify-center"
        >
          
          <v-btn
            color="success"
            type="submit"
            :disabled="invalid"
          >
            Submit
          </v-btn>

          <v-btn
            @click="clear"
            color="warning"
          >
            Clear
          </v-btn>
          
          <v-btn
            :to="{ name: 'clothes'}"
            color="blue-grey"
            dark
          >
            Back
          </v-btn>

        </v-card-actions>       

      </v-card>

    </form>

  </validation-observer>

</template>

<script>
export default {

  props: { cloth: Object },

  data(){
    return {
      modal: false,
      requestData: {},

    }
  },

  computed: {
    formatedDate () {
      return this.cloth.buy_date ? this.moment(this.cloth.buy_date).format('dddd, MMMM Do YYYY') : ''
    },
  },

  methods: {

    submit(){

      if ('id' in this.cloth){
      
        this.clothUpdate();

      }

      else{
        
        this.clothCreate();

      }

    },

    

    clear(){

      this.cloth.forEach(element => {
        element.value = '';
      });
      
    },

    clothCreate(){

      this.axios.post('/api/clothes', this.cloth)
      .then((response) => {
        console.log(response);
      })
      .catch((error) => {
          console.log(error);
      });

    },

    clothUpdate(){

      this.axios.patch('/api/clothes/' + this.cloth.id , this.cloth)
      .then((response) => {
        console.log(response);
      })
      .catch((error) => {
          console.log(error);
      });

    },

  },

  created(){
    console.log(this.cloth);
  }

}
</script>