import axios from 'axios';

axios.defaults.baseURL = '/api/';

// Add a response interceptor
axios.interceptors.response.use(function (response) {

    // Success, 2xx code
    interceptorHandler.handleResponse(response, true);
    return response.data;

  }, function (error) {

    // Fail, not a 2xx code
    interceptorHandler.handleResponse(error, false);

  });


 // Handles every axios request response
 let interceptorHandler = {
    // Determines message icon for sweetAlert
    messageType: null,
    // Modal title
    messageTitle: '',

    // Response entry point
    handleResponse: (response, status) => {
        // Get messages
        let message = response.data.message;
        // Get title
        let title = response.data.title;

        // Get wheter or not they should be shown
        let write = response.data.write;

        // There are messages
        if(message !== undefined){
            // They are supposed to be shown
            if(write){
                // Set title
                interceptorHandler.setTitle(title);
                // Which type of message is suppossed to be shown
                status ? interceptorHandler.messageType = 'success' : interceptorHandler.messageType = 'error';
                // Determine messages format
                interceptorHandler.checkType(message);
            }
            // Log message
            console.log(message);
        }
        // Isn't supposed to happen
        else {
            console.log('Unhandeled Error: there are no messages in the response.',response);
        }
    },

    setTitle: title => {
        // If there is title set it, if there is no title set 'Message' for the title
        title ? interceptorHandler.messageTitle = title : interceptorHandler.messageTitle = 'Message';
    },

    checkType: message => {

        // Messages is an object
        if(typeof message === 'object'){
            interceptorHandler.handleObject(message);
        }

        // Messages is an array
        else if(typeof message === 'array'){
            interceptorHandler.handleArray(message);
        }

        // Message is a string
        else if(typeof message === 'string'){
            interceptorHandler.writeMessage(message);
        }

        // Message is something else
        // Isn't supposed to happen
        else{
            interceptorHandler.handleOther();
        }
    },

    handleObject: message => {
        // Temporary variable to store newly formated message
        let messageToPassFurther = '';

        // Loop through messages
        for(let singleMessage in message){
            messageToPassFurther+=message[singleMessage]+'<br>';
        }
        // Reasign messages variable
        message = messageToPassFurther;
        interceptorHandler.writeMessage(message);
    },

    handleArray: message => {
        // Temporary variable to store newly formated message
        let messageToPassFurther = '';

        // Loop through messages
        for(i=0; i < message.length; i++){
            // STORE MESSAGES INTO STRING
            messageToPassFurther+=message[i]+'<br>';
        }

        // Reasign messages variable
        message = messageToPassFurther;
        interceptorHandler.writeMessage(message);
    },

    handleOther: () => {
        console.log('Error: Message has not been recognized.');
    },

    // WRITE MESSAGE
    writeMessage: message => {
        // Writes message via sweetAlert
        Vue.swal(interceptorHandler.messageTitle, message, interceptorHandler.messageType);
    }
}
