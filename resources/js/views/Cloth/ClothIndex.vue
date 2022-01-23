<template>
  <div>
    <!-- there are no clothes in the DB -->
    <!-- write an appropriate message -->
    <Message
      v-if="clothes.length === 0"
      type="info"
      title="No Clothes found"
      body="There are no clothes found, please insert a cloth to continue."
    ></Message>

    <!-- there are clothes in the DB -->
    <!-- display clothes -->
    <v-data-iterator
      v-else-if="clothes.length > 0"
      :items="clothes"
      :search="dataIterator.search"
      :sort-by="dataIterator.sortBy.toLowerCase()"
      :sort-desc="dataIterator.sortDesc"
      item-key="id"
    >

      <template v-slot:header>

        <v-toolbar
          dark
          color="orange darken-1"
          class="ma-1"
        >

          <v-text-field
            v-model="dataIterator.search"
            clearable
            flat
            solo-inverted
            hide-details
            prepend-inner-icon="mdi-magnify"
            label="Search"
          ></v-text-field>

          <template>

            <!-- <v-spacer></v-spacer>

            <v-select
              v-model="dataIterator.sortBy"
              flat
              solo-inverted
              hide-details
              :items="clothes"
              prepend-inner-icon="mdi-magnify"
              label="Sort by"
            ></v-select> -->

            <v-spacer></v-spacer>

            <v-btn-toggle
              v-model="dataIterator.sortDesc"
              mandatory
            >
              <v-btn
                large
                depressed
                color="orange darken-3"
                :value="false"
              >
                <v-icon>mdi-arrow-up</v-icon>
              </v-btn>

              <v-btn
                large
                depressed
                color="orange darken-3"
                :value="true"
              >
                <v-icon>mdi-arrow-down</v-icon>
              </v-btn>

            </v-btn-toggle>

          </template>

        </v-toolbar>

      </template>

      <template v-slot:default="props">

        <v-row justify="center">

          <v-expansion-panels accordion>

            <v-expansion-panel
              v-for="cloth in props.items"
              :key="cloth.id"
              inset
            >
              <v-expansion-panel-header>

                <v-card-title primary-title>
                  {{ cloth.title }}
                </v-card-title>

              </v-expansion-panel-header>

              <v-expansion-panel-content>

                <v-list-item v-if="cloth.description">

                  <v-list-item-icon>

                    <v-icon>mdi-text</v-icon>

                  </v-list-item-icon>

                  <v-list-item-subtitle>{{ cloth.description }}</v-list-item-subtitle>

                </v-list-item>

                <v-list-item v-if="cloth.buy_at">

                  <v-list-item-icon>

                    <v-icon>mdi-map-marker</v-icon>

                  </v-list-item-icon>

                  <v-list-item-subtitle>{{ cloth.buy_at }}</v-list-item-subtitle>

                </v-list-item>

                <v-list-item v-if="cloth.buy_date">

                  <v-list-item-icon>

                    <v-icon>mdi-calendar</v-icon>

                  </v-list-item-icon>

                  <v-list-item-subtitle>{{ new Date(cloth.buy_date).toLocaleDateString('en-US') }}</v-list-item-subtitle>

                </v-list-item>

                <v-card-actions
                  v-if="!deleteAlert"
                  class="justify-center"
                >

                  <v-btn
                    color="info"
                    @click="clothEdit(cloth)"
                  >
                    Edit
                  </v-btn>

                  <v-btn
                    color="warning"
                    @click=" deleteAlert = true "
                  >
                    Remove
                  </v-btn>

                </v-card-actions>

                <v-alert
                  :value="deleteAlert"
                  text
                  prominent
                  type="error"
                  icon="mdi-exclamation-thick"
                  transition="scale-transition"
                >
                  <p>You are about to delete this clothing item, are you sure?</p>
                  
                  <v-divider
                    class="my-4 error"
                    style="opacity: 0.22"
                  ></v-divider>

                  <div class="text-center">
                    <v-btn
                      color="error"
                      @click="clothDelete(cloth.id)"
                    >
                      Delete
                    </v-btn>

                    <v-btn
                      color="info"
                      @click=" deleteAlert = false"
                    >
                      Cancel
                    </v-btn>
                  </div>

                </v-alert>

              </v-expansion-panel-content>

            </v-expansion-panel>

          </v-expansion-panels>

        </v-row>

      </template>

      <div class="text-center mb-3">

      <v-btn
        color="success"
        class="text-decoration-none"
        :to="{ name: 'clothes.create' }"
        x-large
      >
        Add New Cloth
      </v-btn>

    </div>

    </v-data-iterator>
    
    <!-- create a new piece of cloth -->
    <!-- button -->
    <ButtonCreate
      text="Insert new Cloth"
      @click.native="dialog = !dialog"
    />
    <!-- modal -->
    <v-dialog
      v-model="dialog"
      class="overflow-visible"
      max-width="600px"
      @click:outside="resetClothObject()"
    > 
      <!-- form for createing cloth -->
      <ClothForm
        :cloth = cloth
      />
    </v-dialog>

  </div>
</template>

<script>

import ClothForm from './ClothForm.vue'

export default {

  components: { ClothForm },

  data(){
    return {
      // clothes array
      clothes: [],
      // toggles dialog
      dialog: false,
      // cloth object used for cloth create
      cloth: {
        id: null,
        title: null,
        description: null,
        buy_date: null,
        buy_location: null,
      },
      // sorting mechanisms
      dataIterator: {
        search: '',
        sortDesc: false,
        sortBy: 'title'
      },
      // toggles delete dialog
      deleteAlert: false,
    }
  },

  methods: {
    // load Clothes
    loadClothes(){
      // load Clothes
      this.clothes = this.$store.state.clothes.clothes;
    },

    // deletes Cloth
    clothDelete(id){

      this.axios.delete('/clothes/' + id)
      .then((response) => {
        this.deleteAlert = false;
      })
      .catch((error) => {
        console.log(error);
      });

    },

    // edits Cloth
    clothEdit(cloth){
      // sets cloth data for edit
      for (const key in this.cloth) {
        this.cloth[key] = cloth[key];
      }
      // opens edit dialog
      this.dialog = true;
    },

    // hadles Cloth save
    handleClothSave(){
      // reload Clothes
      this.loadClothes();
      // close Cloth modal
      this.closeClothModal();
    },

    // closes Cloth modal
    closeClothModal(){
      // close Cloth modal
      this.dialog = false;
      // reset Cloth object
      this.resetClothObject();
    },

    // resets cloth object
    resetClothObject(){
      // sets cloth object values to null
      this.cloth = {
        id: null,
        title: null,
        description: null,
        buy_date: null,
        buy_location: null,
      }
    },
  },

  mounted(){
    // load clothes from Vuex store
    this.loadClothes();

    // bus methods
    EventBus.$on("closeClothModal", this.closeClothModal);
    EventBus.$on("handleClothSave", this.handleClothSave);
  },
}
</script>