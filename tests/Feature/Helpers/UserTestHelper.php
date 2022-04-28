<?php

namespace Tests\Feature\Helpers;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTestHelper extends TestCase
{

    // Use Refresh DB.
    use RefreshDatabase;
    
    // Enables proper use of $this.
    function __construct()
    {
        parent::setUp();
    }
  
    /**
     * Checks API repsonse format, wheter or not all keys are present
     * 
     * @param array $response
     */
    private function checkResponseFormat($response)
    {
        $this->assertArrayHasKey('scenario', $response);
        $this->assertArrayHasKey('data', $response);
    }

    /**
     * Registers a User.
     * 
     * @param string $name
     * @param string $email
     * @param string $password
     */
    public function registerUser($name, $email, $password)
    {
        // Prevent sending an actual email.
        Mail::fake();
        // Register User.
        $response = $this->post('api/register', [
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);
        // Check the API response format.
        $this->checkResponseFormat($response);
        // Return response.
        return $response;
    }


    /**
     * Confirms User email.
     * 
     * @param string $email
     */
    public function confirmEmail($email)
    {
        // Get User confirmation token.
        $token = User::where('email', $email)->value('email_confirmation_token');
        // Confrim email.
        $response = $this->post('/api/confirm',
        [
            'token' => $token
        ]);
        // Check the API response format.
        $this->checkResponseFormat($response);
        // Return the response.
        return $response;
    }

    /**
     * Registers and Cofnirms User email.
     * 
     * @param string $username
     * @param string $email
     * @param string $password
     */
    public function registerAndConfirmEmail($username, $email, $password)
    {
        // Register User.
        $this->registerUser($username, $email, $password);
        // Confirm User email.
        return $this->confirmEmail($email);
    }


    /**
     * Logs in User.
     * 
     * @param string $email
     * @param string $password
     */
    public function loginUser($email, $password)
    {
        // Login User.
        $response = $this->post('api/login', [
            'email' => $email,
            'password' => $password,
        ]);
        // Check the API response format.
        $this->checkResponseFormat($response);
        // Return the response.
        return $response;
    }

    /**
     * Sends a forgotten password request.
     * 
     * @param string $email
     */
    public function sendAForgottenPasswordRequest($email)
    {
        // Prevent sending an actual email.
        Mail::fake();
        // Send a forgotten password request.
        $response = $this->post('api/forgot-password', [
            'email' => $email
        ]);
        // Check the API response format.
        $this->checkResponseFormat($response);
        // Return the response.
        return $response;
    }

    /**
     * Registers User and sends a Forgotten Password request.
     * 
     * @param string $username
     * @param string $email
     * @param string $password
     */
    public function registerSendForgottenPasswordRequest($username, $email, $password)
    {
        // Register User.
        $this->registerUser($username, $email, $password);
        // Send a forgotten password request.
        return $this->sendAForgottenPasswordRequest($email);
    }

    /**
     * Registers and confirms User, then sends a Forgotten Password request.
     * 
     * @param string $username
     * @param string $email
     * @param string $password
     */
    public function registerConfirmSendAForgottenPasswordRequest($username, $email, $password)
    {
        // Register User.
        $this->registerUser($username, $email, $password);
        // Confirm User email.
        $this->confirmEmail($email);
        // Send a forgotten password request.
        return $this->sendAForgottenPasswordRequest($email);
    }

    /**
     * Verifies forgotten password token.
     * 
     * @param string $token
     */
    public function verifyForgottenPasswordToken($token)
    {
        // Verify forgotten password token.
        $response = $this->get('api/forgot-password/' . $token);
        // Check the API response format.
        $this->checkResponseFormat($response);
        // Return the response.
        return $response;
    }

    /**
     * Get forgotten password verification token.
     * 
     * @param string $email
     */
    public function getForgottenPasswordToken($email)
    {
        // Get appropriate forgotten password table row.
        $row = DB::table('password_resets')->where('email', $email)->first();
        // There was an row found.
        $this->assertNotNull($row);
        // Verify forgotten password token.
        return $row->token;
    }

    /**
     * Get forgotten password verification token and verify it.
     * 
     * @param string $email
     */
    public function getAndVerifyForgottenPasswordToken($email)
    {
        // Register and confirm User, then send a Forgotten Password request.
        $this->registerConfirmSendAForgottenPasswordRequest('user1234', $email, 'Pswd@123');
        // Get forgotten password token.
        $token = $this->getForgottenPasswordToken($email);
        // Verify forgotten password token.
        return $this->verifyForgottenPasswordToken($token);
    }

    /**
     * Changes password through forgotten password request.
     * 
     * @param string $email
     * @param string $password
     * @param string $forgotten_password
     */
    public function changePasswordThroughForgottenPassword($email, $password, $password_confirmation){
        // Get forgotten password token.
        $token = $this->getForgottenPasswordToken($email);
        // Change password through forgotten password request.
        $response =  $this->patch('api/forgot-password', [
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password_confirmation
        ]);
        // Check the API response format.
        $this->checkResponseFormat($response);
        // Return the response.
        return $response;
    }

    /**
     * Changes password through forgotten password request.
     * 
     * @param string $email
     * @param string $password
     * @param string $forgotten_password
     */
    public function changePasswordThroughForgottenPasswordInvalid($email, $password, $password_confirmation){
        // Get forgotten password token.
        $token = $this->getForgottenPasswordTokenInvalid($email);
        // Change password through forgotten password request.
        $response =  $this->patch('api/forgot-password', [
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password_confirmation
        ]);
        // Check the API response format.
        $this->checkResponseFormat($response);
        // Return the response.
        return $response;
    }

    /**
     * Get forgotten password verification token with an invalid token.
     * 
     * @param string $email
     */
    public function getForgottenPasswordTokenInvalid($email)
    {
        // Get appropriate forgotten password table row.
        $row = DB::table('password_resets')->where('email', $email)->first();
        // There was an row found.
        $this->assertNull($row);
        // Verify forgotten password token.
        return $row;
    }
}