import axios from 'axios';
import { data } from 'jquery';

axios.defaults.baseURL = '/api/';

// Add a response interceptor.
// Success, 2xx code.
axios.interceptors.response.use(function (response) {
  // Check for sweetAlert messages.
  sweetAlertHandler.lookForScenario(response['data']);
  // Return response data.
  return response['data'];
},

// Fail, not a 2xx code.
function (error) {
  sweetAlertHandler.lookForScenario(error.response.data);
  // Return error data.
  return Promise.reject(error.response.data);

});

// List of scenarios when sweetAlert is suppossed to trigger.
let sweetAlertScenarios = {
  // Cloth.
  // Cloth store.
  'cloth.store.success': {
    title: 'Cloth saved',
    message: 'Cloth has been stored.',
    type: 'success'
  },
  'cloth.store.failed.validation': {
    title: 'Cloth store failed',
    message: 'There was an request error while trying to save Cloth.',
    type: 'error'
  },
  // Cloth update.
  'cloth.update.success': {
    title: 'Cloth update successful',
    message: 'Cloth has been updated.',
    type: 'success'
  },
  'cloth.update.wrong-user': {
    title: 'Cloth update failed',
    message: 'Cloth does not belong to the currently logged in User.',
    type: 'error'
  },
  // Cloth destroy.
  'cloth.destroy.success': {
    title: 'Cloth deletion successful',
    message: 'Cloth has been deleted.',
    type: 'success'
  },
  'cloth.destroy.failed.wrong-user': {
    title: 'Cloth deletion failed',
    message: 'Cloth does not belong to the currently logged in User.',
    type: 'error'
  },
  // Cloth and Day.
  // Cloth and Day store.
  'cloth-day.store.success': {
    title: 'Wearing Cloth saved',
    message: 'Wearing data has been saved.',
    type: 'success'
  },
  'cloth-day.store.failed.wrong-user': {
    title: 'Wearing Cloth failed',
    message: 'Cloth does not belong to the currently logged in User.',
    type: 'error'
  },
  'cloth-day.store.failed.wrong-date': {
    title: 'Wearing Cloth failed',
    message: 'Day data already exist for this User.',
    type: 'error'
  },
  'cloth-day.store.failed.validation': {
    title: 'Wearing Cloth failed',
    message: 'There was an request error while trying to save Cloth wearing data.',
    type: 'error'
  },
  // Cloth and Day update.
  'cloth-day.update.success': {
    title: 'Wearing Cloth saved',
    message: 'Wearing data has been saved.',
    type: 'success'
  },
  'cloth-day.update.failed.wrong-day': {
    title: 'Wearing Cloth failed',
    message: 'Date data does not belong to the currently logged in User.',
    type: 'error'
  },
  'cloth-day.update.failed.wrong-clothes': {
    title: 'Wearing Cloth failed',
    message: 'Cloth does not belong to the currently logged in User.',
    type: 'error'
  },
  'cloth-day.update.failed.validation': {
    title: 'Wearing Cloth failed',
    message: 'There was an request error while trying to save Cloth wearing data.',
    type: 'error'
  },
  // Cloth and Day destroy.
  'cloth-day.destroy.success': {
    title: 'Wearing Cloth removed',
    message: 'Wearing data has been removed.',
    type: 'success'
  },
  'cloth-day.destroy.failed.wrong-user': {
    title: 'Wearing Cloth failed',
    message: 'Day data does not belong to the currently logged in User.',
    type: 'error'
  },
  // User.
  // Registration.
  'registration.success': {
    title: 'Registration success',
    message: 'Please verify email to continue.',
    type: 'success'
  },
  'registration.failed.validation': {
    title: 'Registration failed',
    message: 'There was an request error while trying to register.',
    type: 'error'
  },
  'registration.failed.unknown': {
    title: 'Registration failed',
    message: 'There was an unknown error while trying to register. Please contact administrator.',
    type: 'error'
  },
  // Verification.
  'verification.success': {
    title: 'Verification success',
    message: 'You may login now.',
    type: 'success'
  },
  'verification.success.already': {
    title: 'Verification success',
    message: 'This account has already been verified.',
    type: 'success'
  },
  'verification.failed.expired': {
    title: 'Verification failed',
    message: 'Verification link has expired. A new link has been sent.',
    type: 'error'
  },
  'verification.failed.validation': {
    title: 'Verification failed',
    message: 'Invalid verification token.',
    type: 'error'
  },
  // Login.
  'login.success': {
    title: 'Login successful',
    message: 'Thank you for logging in.',
    type: 'success'
  },
  'login.failed.not-verified': {
    title: 'Login failed',
    message: 'Please verify your account first.',
    type: 'error'
  },
  'login.failed.invalid': {
    title: 'Login failed',
    message: 'Invalid email and/or password.',
    type: 'error'
  },
  'login.failed.validation': {
    title: 'Login failed',
    message: 'There was an request error while trying to login.',
    type: 'error'
  },
  // Logout.
  'logout.success': {
    title: 'Logout successful',
    message: 'We hope to see you soon.',
    type: 'success'
  },
  'logout.failed': {
    title: 'Logout failed',
    message: 'There was an error but you have been logged out.',
    type: 'error'
  },
  // Forgotten password.
  'forgotten.failed.email': {
    title: 'Forgotten Password Failed',
    message: 'We couldn\'t find the email address, please make sure that you\'ve used the correct one.',
    type: 'error'
  },
  'forgotten.failed.unverified': {
    title: 'Forgotten Password Failed',
    message: 'Please verify your email first.',
    type: 'error'
  },
  'forgotten-password.failed.token': {
    title: 'Forgotten Password Failed',
    message: 'Invalid validation token, please make a new forgotten password email.',
    type: 'error'
  },
  'forgotten.failed.expired': {
    title: 'Forgotten Password Failed',
    message: 'It\'s been more than 8 hours and token has expired. We\'ve sent you a new one via email.',
    type: 'error'
  },
  'forgotten.success.sent': {
    title: 'Forgotten Password Success',
    message: 'Please check your email for further details.',
    type: 'success'
  },
  'forgotten.success.changed': {
    title: 'Forgotten Password Success',
    message: 'You have successfully changed your password and may login now.',
    type: 'success'
  }
};


// Checks for sweetAlert messages.
let sweetAlertHandler = {
  // Writes message via sweetAlert.
  runSweetAlert: (title, message, type) => {
    // Write message via sweetAlert.
    Vue.swal(title, message, type);
  },

  // Attempts to find the sweetAlert scenario.
  lookForScenario: (response) => {
    // Attempt to find the sweetAlert scenario.
    for(let possibleScenario in sweetAlertScenarios){
      // There is a SweetAlert scenario used for this case.
      if(possibleScenario === response.scenario){
        let scenario = sweetAlertScenarios[possibleScenario];
        // Write message via sweetAlert.
        sweetAlertHandler.runSweetAlert(
          scenario.title,
          scenario.message,
          scenario.type
        );
        // Stop the loop.
        break;
      }
    }
  }
}

// Send authorization token with each request,
axios.interceptors.request.use(
  (config) => {
    let vuex = localStorage.getItem('vuex');

    if(vuex){
      let token = JSON.parse(vuex).auth.token;
      config.headers['Authorization'] = `Bearer ${ token }`;
    }

    return config;
  }, 

  (error) => {
    return Promise.reject(error);
  }
);