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
          
        </v-list-item-group>
      </v-list>
    </v-navigation-drawer>
  </div>
</template>


<script>
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
  }
</script>