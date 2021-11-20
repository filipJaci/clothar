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
            v-for="field in textFields"
            :key="field.title"
            v-slot="{ errors }"
            :name="field.title"
            :rules="field.rules"
          >

            <v-text-field
              v-model="field.value"
              :error-messages="errors"
              :label="field.title"
            ></v-text-field>

          </validation-provider>

          <validation-provider
            v-for="(field, index) in dateFields"
            :key="index"
            :name="field.title"
            :rules="field.rules"
            class="d-block text-center"
          >

            <v-dialog
              ref="dialog"
              v-model="modal"
              :return-value.sync="field.value"
              persistent
              width="290px"
            >

              <template v-slot:activator="{ on, attrs }">

                <v-text-field
                  v-model="field.value"
                  :label="field.title"
                  prepend-icon="mdi-calendar"
                  readonly
                  v-bind="attrs"
                  v-on="on"
                ></v-text-field>

              </template>

              <v-date-picker
                v-model="field.value"
                scrollable
              >

                <v-spacer></v-spacer>

                <v-btn 
                  text
                  color="primary"
                  @click="$refs.dialog[index].save(null)"
                >
                  Clear
                </v-btn>

                <v-btn
                  text
                  color="primary"
                  @click="$refs.dialog[index].save(field.value)"
                >
                  OK
                </v-btn>

              </v-date-picker>

            </v-dialog>
          
          </validation-provider>
        
        </v-card-text>

        <v-card-text class="d-block text-center">

          <v-btn
            class="mr-4"
            type="submit"
            :disabled="invalid"
          >
            submit
          </v-btn>

          <v-btn @click="clear">
            clear
          </v-btn>
        
        </v-card-text>

        

      </v-card>

    </form>

  </validation-observer>
  
</template>

<script>

export default {
  data(){
    return{
      textFields: [
        {
          name: 'title',
          title: 'Title',
          value: '',
          type: 'text',
          rules: 'required',
          errors: [],
        },
        {
          name: 'description',
          title: 'Description',
          value: '',
          type: 'text',
          rules: '',
          errors: [],
        },
        {
          name: 'buy_at',
          title: 'Bought at',
          value: '',
          type: 'text',
          rules: '',
          errors: [],
        }
      ],

      dateFields: [
        {
          name: 'buy_date',
          title: 'Bought on',
          value: null,
          type: 'date',
          rules: '',
          errors: [],
        },
      ],

      alreadySetFields:[

        {
          name: 'status',
          value: 1
        },

        {
          name: 'category',
          value: 1
        }
        
      ],

      modal: false,

      requestData: {},

    }
  },

  methods: {
    submit(){
      
      this.setRequestData();

      console.log(this.requestData);
      
      this.axios.post('/api/clothes', this.requestData)
      .then((response) => {
        console.log(response);
      })
      .catch((error) => {
          console.log(error);
      })

    },

    clear(){
      this.textFields.title.value = '';
      this.textFields.description.value = '';
      this.textFields.buy_at.value = '';

      this.dateFields.buy_date.value = null;
    },

    setRequestData(){

      this.textFields.forEach(textField => {
        this.requestData[textField.name] = textField.value;
      });

      this.dateFields.forEach(dateField => {
        this.requestData[dateField.name] = dateField.value;
      });

      this.alreadySetFields.forEach(alreadySetField => {
        this.requestData[alreadySetField.name] = alreadySetField.value;
      });

    }

  }
}
</script>