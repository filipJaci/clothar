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
            x-large
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

    </form>

  </validation-observer>

</template>

<script>
import { mapActions } from 'vuex';

export default {

  name: 'cloth.form',

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
    // Registers a Vuex method.
    ...mapActions({
      // saveClothes refers to saveClothes method found in clothes.
      saveClothes: 'clothes/saveClothes'
    }),

    // Handles click on the Submit button.
    submit(){
      // There is Cloth id.
      if (this.cloth.id !== null){
        // Update Cloth.
        this.clothUpdate();
      }
      // There is no Cloth id.
      else{
        // Create a new Cloth.
        this.clothCreate();
      }
    },
    // Handles click on the Clear button.
    clear(){
      // Resets all values on Cloth object.
      for(let value in this.cloth){
        this.cloth[value] = '';
      }
      
    },
    // Creates a new Cloth.
    clothCreate(){
      // Send a create Cloth request.
      // With Cloth object.
      this.axios.post('/clothes', this.cloth)
      .then((response) => {
        // Run Vuex saveClothes method.
        this.saveClothes(response['data']);
        // Run handleClothSave Bus method on ClothIndex.
        EventBus.$emit('handleClothSave');
      })
      .catch((error) => {});

    },
    // Updates an exsisting Cloth.
    clothUpdate(){
      // Send a update Cloth request.
      // With Cloth object.
      this.axios.patch('/clothes', this.cloth)
      .then((response) => {
        // Run Vuex saveClothes method.
        this.saveClothes(response['data']);
        // Run handleClothSave Bus method on ClothIndex.
        EventBus.$emit('handleClothSave');
      })
      .catch((error) => {});

    },

  }
}
</script>