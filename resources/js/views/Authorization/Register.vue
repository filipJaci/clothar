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

            <v-card-title>Register</v-card-title>

            <v-card-text>

              <validation-provider
                v-slot="{ errors }"
                name="name"
                :rules="user.name.rules"
              >

                <v-text-field
                  v-model="user.name.value"
                  :error-messages="errors"
                  label="Name"
                ></v-text-field>

              </validation-provider>

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

              <validation-provider
                v-slot="{ errors }"
                name="password_confirmation"
                :rules="user.password_confirmation.rules"
              >

                <v-text-field
                  v-model="user.password_confirmation.value"
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

              <div class="col-12 text-center">
                <label>Already have an account? <router-link :to="{name:'login'}">Login Now!</router-link></label>
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
    </v-card>
  </v-container>
</template>

<script>

export default {

  name:'register',

  data(){
    return {
      // Registration credentials.
      user:{
        name: {
          value: '',
          rules: {
            'required': true,
            'alpha_dash': true,
            'min': 8,
            'max': 40
          }
        },
        email: {
          value: '',
          rules: {
            'required': true,
            'email': true
          }
        },
        password: {
          value: '',
        },
        password_confirmation: {
          value: '',
          rules: {
            'required': true,
            'confirmed': 'password'
          }
        }
      },
      // Toggles register button.
      processing: false
    }
  },
  methods:{
    // Registers a User.
    submit(){
      // Disable register button.
      this.processing = true;
      // Register a User.
      axios.post('/register', {
        'name': this.user.name.value,
        'email': this.user.email.value,
        'password': this.user.password.value
      })
      // Registeration success.
      .then(response=> {
        // Push to login.
        this.$router.push('login');
      })
      // Registration failed.
      .catch(error => {
        // Enable register button.
        this.processing = false;
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

  }
}
</script>