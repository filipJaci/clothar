<template>
  <div>
    <v-system-bar color="green darken-4"></v-system-bar>

    <v-app-bar color="green darken-2" dark prominent>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>

      <v-toolbar-title>Clothar</v-toolbar-title>

      <v-spacer></v-spacer>

      <v-btn icon>
        <v-icon>mdi-magnify</v-icon>
      </v-btn>

      <v-btn icon>
        <v-icon>mdi-filter</v-icon>
      </v-btn>

      <v-btn icon>
        <v-icon>mdi-dots-vertical</v-icon>
      </v-btn>
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" absolute temporary>
      <v-list nav dense>
        <v-list-item-group
          v-model="group"
          active-class="green darken-2 white--text"
        >
          <v-list-item
            :to="{ name: 'clothes' }"
            class="text-decoration-none"
          >
            Clothes
          </v-list-item>

          <v-list-item
            :to="{ name: 'days' }"
            class="text-decoration-none"
          >
            Days
          </v-list-item>

          <v-list-item
            @click="logout()"
          >
            logout
          </v-list-item>
          
        </v-list-item-group>
      </v-list>
    </v-navigation-drawer>
  </div>
</template>


<script>
import { mapActions } from 'vuex';

export default {
  data: () => ({
    drawer: false,
    group: null,
  }),

  watch: {
    group () {
      this.drawer = false
    },
  },

  methods: {
    // registers a Vuex method
    ...mapActions({
      // logout refers to logout method found in auth
      authLogout: 'auth/logout',
      // saveClothes refers to saveClothes method found in clothes
      removeClothes: 'clothes/removeClothes',
      // removeDays refers to removeDays method found in days
      removeDays: 'days/removeDays'
    }),

    // logsout user
    logout() {

      // get CSRF cookie
      this.axios.get('/sanctum/csrf-cookie').then(response => {

        // logout
        this.axios.post('logout')
        // success
        .then(response => {
          // run Vuex methods
          this.authLogout();
          this.removeClothes();
          this.removeDays();
          // run handleLogout bus method on App
          EventBus.$emit('handleLogout');
          // push to login page
          this.$router.push('login');
        })
        // there was an error with the request
        .catch(function (error) {
          console.error(error);
        });

      });
    }
  }
}
</script>