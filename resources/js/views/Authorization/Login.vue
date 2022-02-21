<template>
<div class="container h-100">
  <div class="row h-100 align-items-center">
    <div class="col-12 col-md-6 offset-md-3">
      <div class="card shadow sm">
        <div class="card-body">
        <h1 class="text-center">Login</h1>
        <hr/>
        <form action="javascript:void(0)" class="row" method="post">
          <div class="form-group col-12">
            <label for="email" class="font-weight-bold">Email</label>
            <input type="text" v-model="auth.email" name="email" id="email" class="form-control">
          </div>
          <div class="form-group col-12">
            <label for="password" class="font-weight-bold">Password</label>
            <input type="password" v-model="auth.password" name="password" id="password" class="form-control">
          </div>
          <div class="col-12 mb-2">
            <button type="submit" :disabled="processing" @click="login" class="btn btn-primary btn-block">
            {{ processing ? "Please wait" : "Login" }}
            </button>
          </div>
          <div class="col-12 text-center">
            <label>Don't have an account? <router-link :to="{name:'register'}">Register Now!</router-link></label>
          </div>
        </form>
        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script>

import { mapActions } from 'vuex';

export default {

  name:"login",

  data(){
    return {
      // login credentials
      auth:{
        email:"",
        password:""
      },
      // toggles login button
      processing: false
    }
  },

  methods:{
    // registers a Vuex method
    ...mapActions({
      // signIn refers to login method found in auth
      signIn: 'auth/login',
      // saveClothes refers to saveClothes method found in clothes
      saveClothes: 'clothes/saveClothes',
      // saveDays refers to saveDays method found in clothes
      saveDays: 'days/saveDays'
    }),

    // logs in user
    login(){

      //get CSRF cookie
      this.axios.get('/sanctum/csrf-cookie').then(response => {
        // run login request
        this.axios.post('login', this.auth)
        // login successful
        .then(response => {
          // run Vuex signIn method
          this.signIn(response['data']);
          // run Vuex saveClothes method
          this.saveClothes(response['data']['clothes']);
          // run Vuex saveDays method
          this.saveDays(response['data']['days']);
          // run handleLogin bus method on App
          EventBus.$emit('handleLogin');
        })
        // login failed
        .catch(error => {});
      })},
  }
}
</script>