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
  
  // API response.
  public $response = [
    // Scenario.
    'scenario' => '',
    // Additional data.
    'data' => [
      // Used in case of an unknown error.
      'error' => '',
      // Used for session.
      'token' => '',
      // User information.
      'user' => [
        // User id.
        'id' => '',
        // Username.
        'username' => '',
      ],
      // Clothes.
      'clothes' => [],
      // Days.
      'days' => [],
    ]
  ];

  // API response code.
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
      Mail::to($user->email)->send(new EmailConfirmation($user->email_verification_token));

      // Set API response code
      $this->code = 200;

      // Set API response scenario.
      $this->response['scenario'] = 'registration.success';
    }
    // Failed registration.
    catch (QueryException $ex) {
      // Set API response code.
      // 400 - Bad request - broader than conflict.
      $this->code = 400;
      // Set API response scenario.
      $this->response['scenario'] = 'registration.failed.unknown';
      // Send error information.
      // $this->response['data']['error'] = $ex;

    }

    // Return the response.
    return response()->json($this->response, $this->code);
  }

  /**
   * Verification
   */
  public function verifyEmail(EmailConfirmationRequest $request){

    // Get User that needs to be verified.
    $user = User::where('email_verification_token', $request->token)->first();
    
    // User is already verified.
    if($user->email_verified){
      // Set API response code.
      $this->code = 200;
      // Set API response scenario.
      $this->response['scenario'] = 'verification.success.already';
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
        // Set API response scenario.
        $this->response['scenario'] = 'verification.success';
      }
      // User registered more than 8 hours ago.
      else{
        // Generate new verification token.
        $user->generateVerificationToken();
        // Send confirmation email.
        Mail::to($user->email)->send(new EmailConfirmation($user->email_verification_token));
        // Save changes.
        $user->save();
        // Set API response code.
        $this->code = 410;
        // Set API response scenario.
        $this->response['scenario'] = 'verification.failed.expired';
      }
    }

    // Return the response.
    return response()->json($this->response, $this->code);
  }

  /**
   * Login
   */
  public function login(UserLoginRequest $request){
    // Login credentials.
    $credentials = [
      'email' => $request->email,
      'password' => $request->password,
    ];

    // Succusseful login.
    if (Auth::attempt($credentials)) {
      // User is verified.
      if(auth()->user()->email_verified){
        // Set API success code.
        $this->code = 200;
        // Set API response scenario.
        $this->response['scenario'] = 'login.success';
        // Set and store session token.
        $this->response['data']['token'] = auth()->user()->createToken('API Token')->plainTextToken;
        // Set user infomrmation.
        $this->response['data']['user']['id'] = auth()->id();
        $this->response['data']['user']['name'] = auth()->user()->name;
        $this->response['data']['clothes'] = Cloth::all();
        $this->response['data']['days'] = Day::with('clothes')->get();
      }
      // User is not verified.
      else{
        // Drop session.
        Session::flush();
        // Set API status code to 403 - Forbidden.
        $this->code = 403;
        // Set API response scenario.
        $this->response['scenario'] = 'login.failed.not-verified';
      }
    }
    
    // Failed login.
    else {
      // Set API unaothorized code.
      $this->code = 401;
      // Set API response scenario.
      $this->response['scenario'] = 'login.failed.invalid';
    }
    
    // Send API response.
    return response()->json($this->response, $this->code);
  }

  /**
  * Logout
  */
  public function logout(){

    // Attempt to logout.
    try {
      // Drop session.
      Session::flush();
      // Set API success code.
      $this->code = 200;
      // Set API response scenario.
      $this->response['scenario'] = 'logout.success';
    }
    
    catch (QueryException $ex) {
      // Set API response code.
      // 400 - Bad request - broader than conflict.
      $this->code = 400;
      // Set API response scenario.
      $this->response['scenario'] = 'logout.failed';
    }

    // Send API response.
    return response()->json($this->response, $this->code);
  }
}