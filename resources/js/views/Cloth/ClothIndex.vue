<template>
  <div>

    <Message
      v-if="clothes.length === 0"
      type="info"
      title="No Clothes found"
      body="There is no existing data, please insert a cloth to continue."
    ></Message>

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
                    :to="{ name: 'clothes.edit', params: { id: cloth.id }, props: { cloth: cloth }}"
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

    <ButtonCreate
      scenario = 'cloth'
      origin = 'ClothIndex'
    ></ButtonCreate>

  </div>
</template>

<script>

export default {

  props: {
    clothes: Array
  },

  data(){
    return {
      dataIterator: {
        search: '',
        sortDesc: false,
        sortBy: 'title'
      },

      deleteAlert: false,
    }
  },

  methods: {
    clothDelete(id){

      this.axios.delete('/clothes/' + id)
      .then((response) => {
        this.deleteAlert = false;
        // run getClothes bus method on ClothIndex
        EventBus.$emit('getClothes');
      })
      .catch((error) => {
        console.log(error);
      });

    }
  }
}
</script>