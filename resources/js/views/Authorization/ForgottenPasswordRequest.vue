<template>
  <v-container grid-list-xs>
    <v-card
      max-width="600"
      class="mx-auto"
    >
      <validation-observer
        ref="observer"
        v-slot="{ invalid }"
      >
      
        <form @submit.prevent="submit">

          <v-card>

            <v-card-title>Forgotten password</v-card-title>

            <v-card-text>

              </validation-provider>

              <validation-provider
                v-slot="{ errors }"
                name="email"
                :rules="email.rules"
              >

                <v-text-field
                  v-model="email.value"
                  :error-messages="errors"
                  label="Email"
                ></v-text-field>

              </validation-provider>
            
            </v-card-text>

            <v-card-actions
              class="d-flex justify-center"
            >
              
              <v-btn
                color="success"
                type="submit"
                :disabled="submitButton || invalid"
                x-large
              >
                Submit
              </v-btn>

              <v-btn
                @click="email.value = ''"
                color="warning"
                x-large
                :disabled="emailValueIsEmpty"
              >
                Clear
              </v-btn>

            </v-card-actions>
        
              <div class="col-12 text-center">
                <label><router-link :to="{name:'login'}">Go back to Login.</router-link></label>
              </div>

          </v-card>

        </form>

      </validation-observer>
    </v-card>
  </v-container>
</template>

<script>
export default {
    data(){
        return{
            // Email associated with the account that password is being changed on.
            email: {
                // Input field value.
                value: '',
                // Rules used for the Front-End validation.
                rules: 'email|required'
            },
            // Toggles wether or not submit button should be disabled.
            submitButton: false
        }
    },
    methods: {
        // Toggles wether or not submit button is disabled.
        toggleSubmiButton(disabled){
            // Toggle submit button.
            this.submitButton = disabled;
        },
        // Handles click on the Submit button.
        submit(){
            // Disable submit button.
            this.toggleSubmiButton(true);
            // Convert email to lowerstring.
            this.email.value = this.email.value.toLowerCase();
            // Sends a Forgotten Password create request.
            axios.post('forgot-password', {
                email: this.email.value
            });
            // Enable submit button.
            this.toggleSubmiButton(false);
        }
    }
}
</script>

<style>

</style>