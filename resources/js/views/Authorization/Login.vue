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

            <v-card-title>Login</v-card-title>

            <v-card-text>

              <validation-provider
                v-slot="{ errors }"
                name="email"
                :rules="user.email.rules"
              >

                <v-text-field
                  v-model="user.email.value"
                  :error-messages="errors"
                  label="Email"
                ></v-text-field>

              </validation-provider>

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
                  v-model="user.password.value"
                  :error-messages="errors"
                  label="Password"
                  type="password"
                ></v-text-field>

              </validation-provider>

              <div class="col-12 text-center">
                <label>Don't have an account? <router-link :to="{name:'register'}">Register Now!</router-link></label>
              </div>
              
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

      <input type="text" v-model="validation">
      <button @click="validate">Validate</button>
    </v-card>
  </v-container>
</template>

<script>

import { mapActions } from 'vuex';

export default {

  name:"login",

  data(){
    return {
      // Login credentials.
      user:{
        email: {
          value: '',
          rules: {
            'required': true,
            'email': true
          }
        },
        password: {
          value: '',
        }
      },
      // Toggles login button.
      processing: false,
      validation: ''
    }
  },

  methods:{
    // Registers Vuex methods.
    ...mapActions({
      // signIn refers to login method found in auth
      signIn: 'auth/login',
      // saveClothes refers to saveClothes method found in clothes
      saveClothes: 'clothes/saveClothes',
      // saveDays refers to saveDays method found in clothes
      saveDays: 'days/saveDays'
    }),

    // Logs in User.
    submit(){
      // Disable login button.
      this.processing = true;
      //Get CSRF cookie.
      this.axios.get('/sanctum/csrf-cookie').then(response => {
        // Run login request.
        this.axios.post('login', {
        'email': this.user.email.value,
        'password': this.user.password.value
        })
        // Login successful.
        .then(response => {
          // Run Vuex signIn method.
          this.signIn(response['data']);
          // Run Vuex saveClothes method.
          this.saveClothes(response['data']['clothes']);
          // Run Vuex saveDays method.
          this.saveDays(response['data']['days']);
          // Run handleLogin bus method on App.
          EventBus.$emit('handleLogin');
        })
        // Login failed.
        .catch(error => {
          // Enable login button.
          this.processing = false;
        });
      });
    },

    // Clears all input fields.
    clear(){
      // Itterate through all input fields.
      for(let value in this.user){
        // Reset input field.
        this.user[value] = '';
      }
    },
    // Verifies User.
    verify(){
      // Get verification token from URL.
      let token = this.validation;

      // Run verification request.
      this.axios.post('verify/',{
        'token': token
      })
      // Verification successful.
      .then(response => {
        // Set message scenario.
        this.scenario = response.scenario;
        // Verification ended.
        this.verificationInProgress = false;
      })
      // Verification failed.
      .catch(error => {
        // Set message data.
        this.scenario = error.scenario;
        // Verification ended.
        this.verificationInProgress = false;
      });


    }
  }
}
</script>