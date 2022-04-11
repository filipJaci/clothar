<template>
    <!-- Once verification is done. -->
    <!-- Write an appropriate message. -->
    <Message
      v-if="! verificationInProgress" 
      :scenario="scenario"
    ></Message>

</template>

<script>
export default {
  data(){
    return{
      // User is being verified.
      verificationInProgress: true,
      // Scenario is used to determine the message that appears.
      scenario: ''
    }
  },
  methods: {
    // Verifies User.
    verify(){
      // Get verification token from URL.
      let token = this.$route.params.token;

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
  },

  mounted(){
    // Run verify method.
    this.verify();
  }
}
</script>