<?php

namespace App\Http\Controllers\API;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

use App\Models\User;
use App\Models\Cloth;
use App\Models\Day;

use App\Mail\EmailConfirmation;

use App\Http\Requests\UserRegistrationRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\EmailConfirmationRequest;

class UserController extends Controller {
  
  // API response
  public $response = [
    // title
    'title' => '',
    // message
    'message' => '',
    // message should be displayed
    'write' => true,
    // additional data
    'data' => [
      // used for session
      'token' => '',
      // user information
      'user' => [
        // user id
        'id' => '',
        // username
        'username' => '',
      ],
      // clothes
      'clothes' => [],
      // days
      'days' => [],
    ]
  ];

  // API response code
  public $code = null;

  /**
   * Register
   */
  public function register(UserRegistrationRequest $request){

    // Successful registration.
    try {
      // Make a new User instance.
      $user = new User();

      // Generate verification token.
      $user->generateVerificationToken();

      // Set field values.
      $user->name = $request->name;
      $user->email = $request->email;
      $user->password = Hash::make($request->password);
      $user->email_verified = false;

      // Save User.
      $user->save();

      // Send confirmation email.
      Mail::to($user->email)->send(new EmailConfirmation());

      // Set API response code
      $this->code = 200;

      // Set API response messages.
      $this->response['title'] = 'Registration successful';
      $this->response['message'] = 'You may Login now.';
    }
    // Failed registration.
    catch (QueryException $ex) {

      if($ex->errorInfo[2] === 'UNIQUE constraint failed: users.email'){
        // Set API response code.
        // 409 - Conflict - indication that changing credentials would help.
        $this->code = 409;
        // Set API response messages.
        $this->response['title'] = 'Registration failed';
        $this->response['message'] = 'This email is already registered.';
      }
      else if($ex->errorInfo[2] === 'UNIQUE constraint failed: users.name'){
        // Set API response code.
        // 409 - Conflict - indication that changing credentials would help.
        $this->code = 409;
        // Set API response messages.
        $this->response['title'] = 'Registration failed';
        $this->response['message'] = 'This username is already registered.';
      }
      else{
        // Set API response code.
        // 400 - Bad request - broader than conflict.
        $this->code = 400;
        $this->response['title'] = 'Registration failed';
        $this->response['message'] = 'Unknown error.';
      }

    }

    // Return the response.
    return response()->json($this->response, $this->code);
  }

  // Verification.
  public function verifyEmail(EmailConfirmationRequest $request){
    // Set API response title.
    $this->response['title'] = 'Verification attempt';

    // Get User that needs to be verified.
    $user = User::where('email_verification_token', $request->token)->first();
    // User is already verified.
    if($user->email_verified){
      // Set API response code.
      $this->code = 200;
      // Set API response message.
      $this->response['message'] = 'This User has already been verified, you may log in.';
    }
    // User hasn't been verified before.
    else{
      // User registered less than 8 hours ago.
      if($user->updated_at->diffInHours() < 8){
        // Set API response code.
        $this->code = 200;
        // Verify User.
        $user->email_verified = true;
        // Save changes.
        $user->save();
        // Set API response message.
        $this->response['message'] = 'Verification successful, you may log in.';
      }
      // User registered more than 8 hours ago.
      else{
        // Generate new verification token.
        $user->generateVerificationToken();
        // Send new confirmation email.
        Mail::to($user->email)->send(new EmailConfirmation());
        // Save changes.
        $user->save();
        // Set API response code.
        $this->code = 410;
        // Set API response message.
        $this->response['message'] = 'Verification failed, link has expired. A new email was sent to your address.';
      }
    }

    // Return the response.
    return response()->json($this->response, $this->code);
  }

  /**
   * Login
   */
  public function login(UserLoginRequest $request){

    // Set API response title.
    $this->response['title'] = 'Login attempt';

    // Login credentials.
    $credentials = [
      'email' => $request->email,
      'password' => $request->password,
    ];

    // Succusseful login.
    if (Auth::attempt($credentials)) {
      // Set API success code.
      $this->code = 200;
      // Set API response message.
      $this->response['message'] = 'Login successful.';
      // Set and store session token.
      $this->response['data']['token'] = auth()->user()->createToken('API Token')->plainTextToken;
      // Set user infomrmation.
      $this->response['data']['user']['id'] = auth()->id();
      $this->response['data']['user']['name'] = auth()->user()->name;
      $this->response['data']['clothes'] = Cloth::all();
      $this->response['data']['days'] = Day::with('clothes')->get();
    }
    
    // Failed login.
    else {
      // Set API unaothorized code.
      $this->code = 401;
      // Set API response message.
      $this->response['message'] = 'Login failed.';
    }
    
    // Send API response.
    return response()->json($this->response, $this->code);
  }

  /**
  * Logout
  */
  public function logout(){
    // Set API response title.
    $this->response['title'] = 'Logout attempt.';

    // Attempt to logout.
    try {
      // Drop session.
      Session::flush();
      // Set API success code.
      $this->code = 200;
      // Set API response message.
      $this->response['message'] = 'Logout successful.';
    }
    
    catch (\Illuminate\Database\QueryException $ex) {
      // Set API response code.
      // 400 - Bad request - broader than conflict.
      $this->code = 400;
      // Set API response message.
      $this->response['message'] = 'Logout failed: - ' . $ex->getMessage();
    }

    // Send API response.
    return response()->json($this->response, $this->code);
  }
}