import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '../store'

Vue.use(VueRouter);

import ClothIndex from '../views/Cloth/ClothIndex.vue';

import DayIndex from '../views/Day/DayIndex.vue';

import Register from '../views/Authorization/Register';
import Login from '../views/Authorization/Login';
import Verification from '../views/Authorization/Verification';
import ForgottenPasswordRequest from '../views/Authorization/ForgottenPasswordRequest';

const routes = [
  {
    name: 'home',
    path: '/',
    redirect: '/clothes',
  },
  {
    name: 'clothes',
    path: '/clothes',
    component: ClothIndex,
    meta:{
      middleware:"auth",
      title: 'Clothes'
    }
  },
  {
    name: 'days',
    path: '/days',
    component: DayIndex,
    meta:{
      middleware:"auth",
      title: 'Days'
    }
  },
  {
    name: 'register',
    path: '/register',
    component: Register,
    meta:{
      middleware:"guest",
      title:`Register`
    }
  },
  {
    name: 'verify',
    path: '/verify/:token',
    component: Verification,
    meta:{
      middleware:"guest",
      title:`Email Verification`
    }
  },
  {
    name: 'login',
    path: '/login',
    component: Login,
    meta:{
      middleware:"guest",
      title:`Login`
    }
  },
  {
    name: 'forgotten-request',
    path: '/forgotten',
    component: ForgottenPasswordRequest,
    meta:{
      middleware:"guest",
      title:`Forgotten password request`
    }
  }
];


var router  = new VueRouter({
  mode: 'history',
  routes: routes
})

router.beforeEach((to, from, next) => {
  document.title = `${to.meta.title} - ${process.env.MIX_APP_NAME}`
  if(to.meta.middleware=="guest"){
    if(store.state.auth.authenticated){
      next({name:"clothes"})
    }
    next()
  }
  else{
    if(store.state.auth.authenticated){
      next();
    }
    else{
      next({name:"login"})
    }
  }
})

export default router