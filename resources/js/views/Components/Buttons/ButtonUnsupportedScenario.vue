<template>

  <Message
    type="error"
    title="Error: Unsuported button scenario"
    :body="errorMessage"
  ></Message>

</template>

<script>
export default {

  props: {
    scenario: String,
    button: String,
    origin: String
  },

  data(){
    return{
      errorMessage: ''
    }
  },

  methods: {
    setErrorMessage(){
      this.errorMessage = 
      'Scenario: "<strong>' + this.scenario +
      '</strong>"<br>Button: "<strong>' + this.button +
      '</strong>"<br>Origin: "<strong>' + this.origin +
      '</strong>"<br><br>Please contact the administrator.';
    }
  },

  created(){
    this.setErrorMessage();
  }
}
</script>