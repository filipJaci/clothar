<template>

  <!-- Appropriate scenario has been set -->
  <div
    class="text-center mb-3"
    v-if=" currentScenario.name !== '' "
  >

    <v-btn
      color="success"
      class="text-decoration-none"
      :to="{ name: currentScenario.routeName }"
      x-large
    >
      {{ currentScenario.buttonText }}
    </v-btn>

  </div>

  <!-- Appropriate scenario has not been set -->
  <ButtonUnsupportedScenario
    v-else
    button = "success"
    :scenario = scenario
    :origin = origin
  ></ButtonUnsupportedScenario>

</template>

<script>

import ButtonUnsupportedScenario from './ButtonUnsupportedScenario.vue';

export default {

  components: { ButtonUnsupportedScenario },

  props: {
    scenario: String,
    origin: String
  },

  data(){
    return{
      scenarios: [
        {
          name: 'cloth',
          buttonText:  'Add new Cloth',
          routeName: 'clothes.create',
        },
        {
          name: 'day',
          buttonText:  'Wear a Cloth',
          routeName: 'days.create'
        }
      ],

      currentScenario: {
        name: '',
        buttonText:  '',
        routeName: ''
      },
    }
  },

  methods: {
    determineScenario(){

      // loop through all supported scenarios
      this.scenarios.forEach(scenario => {
        // assigned scenario matches supported scenario
        if(this.scenario === scenario.name){
          // set current scenario as supported scenario
          this.currentScenario = scenario;
        }
      });

    }
  },

  created(){
    // determine and set appropriate scenario
    this.determineScenario();
  }
  
}
</script>

<style>

</style>