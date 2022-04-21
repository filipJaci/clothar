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

            <v-card-title>Forgotten password - Change password</v-card-title>

            <v-card-text>

              <validation-provider
                v-slot="{ errors }"
                name="password"
                :rules="{ 
                  required: true,
                  min: 8,
                  max: 40,
                  regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/,
                }"
              >

                <v-text-field
                  v-model="password"
                  :error-messages="errors"
                  label="Password"
                  type="password"
                ></v-text-field>

              </validation-provider>

              <validation-provider
                v-slot="{ errors }"
                name="password_confirmation"
                rules="required|confirmed:password"
              >

                <v-text-field
                  v-model="password_confirmation"
                  :error-messages="errors"
                  label="Confirm Password"
                  type="password"
                ></v-text-field>

              </validation-provider>

              <v-alert type="info" outlined>
                Password should contain at least one of the following:<br>
                <ul>
                  <li>Lowercase letter.</li>
                  <li>Uppercase letter.</li>
                  <li>Special symbol: (@$!%*?&).</li>
                </ul>
              </v-alert>

              <v-divider></v-divider>
            
            </v-card-text>

            <v-card-actions
              class="d-flex justify-center"
            >
              
              <v-btn
                color="success"
                type="submit"
                :disabled="invalid || submitButton"
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
    </v-card>
  </v-container>
</template>

<script>
export default {
    data(){
        return{
            // New password.
            password: '',
            // New password confirmation.
            password_confirmation: '',
            // Request ioentifier found in the URL.
            token: '',
            // Toggles wether or not submit button should be disabled.
            submitButton: false
        }
    },
    methods:{
        // Sets value of token.
        setToken(){
            this.token = this.$route.params.token;
        },
        // Toggles wether or not submit button is disabled.
        toggleSubmiButton(disabled){
            // Toggle submit button.
            this.submitButton = disabled;
        },
        // Handles click on the Submit button.
        submit(){
            // Set token.
            this.setToken();
            // Disable submit button.
            this.toggleSubmiButton(true);
            // Sends a Forgotten Password change request.
            axios.patch('forgot-password', {
                token: this.token,
                password: this.password,
            });
            // Enable submit button.
            this.toggleSubmiButton(false);
        },
        // Handles click on the Clear button.
        clear(){
            // Reset input values.
            this.password = '';
            this.password_confirmation = '';
        }
    },
}
</script>

<style>
</style>