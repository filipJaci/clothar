<template>

  <v-alert
    v-if="currentScenario.title !== ''"
    :type="currentScenario.type"
    border="top"
    colored-border
    prominent
    elevation="2"
    class="mt-2"
  >

    <h3>
      {{ currentScenario.title }}
    </h3>

    <v-btn
      v-if="currentScenario.button.show"
      :color="currentScenario.button.type"
    >
      {{ currentScenario.button.text }}
    </v-btn>

    <div v-html="currentScenario.body"></div>
    
  </v-alert>

</template>

<script>
export default {
  props: {
    // Scenario passed from the parent component.
    scenario: String
  },
  data(){
    return{
      // List of all of supported scenarios.
      scenarios: {
        // Not found.
        'notfound': {
          'type': 'info',
          'title': 'Message error',
          'body': 'There was an message error for the scenario: <strong>'+ this.scenario + '</strong><br>Please contact the administrator.',
          'button': {
            'show': false,
          }
        },
        // Cloth.
        'cloth.no-clothes': {
          'type': 'info',
          'title': 'No Clothes found',
          'body': 'Please insert Cloth to continue.',
          'button': {
            'show': false,
          }
        },
        // Verification.
        'verification.failed.validation': {
          'type': 'warning',
          'title': 'Verification failed',
          'body': 'Invalid validation token, please contact administrator.',
          'button': {
            'show': false,
          }
        },
        'verification.success': {
          'type': 'success',
          'title': 'Verification successful',
          'body': 'Thank you for verifying your account, you may log in now.',
          'button': {
            'show': true,
            'text': 'Go to Login',
            'link': '/login'
          }
        },
        'verification.success.already': {
          'type': 'success',
          'title': 'Verification successful',
          'body': 'Email was already verified, you may login.',
          'button': {
            'show': true,
            'text': 'Go to Login',
            'link': '/login'
          }
        },

      },
      // Current scenario.
      currentScenario: {
        'type': '',
        'title': '',
        'body': '',
        'button': {
          'show': false,
        }
      }
    }
  },
  methods:{
    // Sets current scenario.
    setCurrentScenario(){
      // Look through available scenarios and set current scenario.
      for(let possibleScenario in this.scenarios){
        // Scenario found.
        if(possibleScenario === this.scenario){
          // Set scenario.
          this.currentScenario = this.scenarios[possibleScenario];
          // Stop the loop.
          break;
        }
      }

      // No scenario was found.
      if(this.currentScenario.title === ''){
        // Set to notfound scenario.
        this.currentScenario = this.scenarios['notfound'];
      }
    }
  },
  mounted(){
    // Set current scenario.
    this.setCurrentScenario();
  }
};
</script>