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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Cloth;
use App\Models\Day;

use App\Mail\EmailConfirmation;
use App\Mail\ForgottenPasswordEmail;

use App\Http\Requests\UserRegistrationRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\EmailConfirmationRequest;
use App\Http\Requests\SendForgottenPasswordRequest;
use App\Http\Requests\ChangePasswordThroughForgottenPasswordRequest;
use App\Http\Requests\VerifyForgottenPasswordTokenRequest;

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
        // Name.
        'name' => '',
      ],
      // Clothes.
      'clothes' => [],
      // Days.
      'days' => [],
    ]
  ];

  // API response code.
  public $code = null;

  // Set forgotten password token.
  public function setAndSendForgottenPasswordToken($email){
    // Forgotten password token.
    $token = null;
    // While token is null.
    while($token === null){
      // Set new token.
      $newToken = Str::random(10);
      // New token doesn't already exist.
      if(DB::table('password_resets')->where(['token' => $newToken])->count() === 0){
        // Set token to new token.
        $token = $newToken;
      }
    }

    // Create or update a password_resets entry for the User.
    DB::table('password_resets')->updateOrInsert(
      // Criteria on wheter an entry should be created or updated.
      ['email' => $email],
      // New values for the DB.
      [
        'token' => $token,
        'created_at' => Carbon::now()
      ]
    );

    
    // Send confirmation email.
    Mail::to($email)->send(new ForgottenPasswordEmail($token));
  }

  /**
   * Register.
   */
  public function register(UserRegistrationRequest $request){

    // Successful registration.
    try {
      // Make a new User instance.
      $user = new User();

      // Generate confirmation token.
      $user->generateConfirmationToken();

      // Set field values.
      $user->name = $request->name;
      $user->email = $request->email;
      $user->password = Hash::make($request->password);
      $user->email_confirmed = false;

      // Save User.
      $user->save();

      // Send confirmation email.
      Mail::to($user->email)->send(new EmailConfirmation($user->email_confirmation_token));

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
   * Email confirmation.
   */
  public function confirmEmail(EmailConfirmationRequest $request){

    // Get User that needs to be confirmed.
    $user = User::where('email_confirmation_token', $request->token)->first();
    
    // User is already confirmed.
    if($user->email_confirmed){
      // Set API response code.
      $this->code = 200;
      // Set API response scenario.
      $this->response['scenario'] = 'confirmation.success.already';
    }
    // User hasn't been confirmed before.
    else{
      // User registered less than 8 hours ago.
      if($user->updated_at->diffInHours() < 8){
        // Set API response code.
        $this->code = 200;
        // Verify User.
        $user->email_confirmed = true;
        // Save changes.
        $user->save();
        // Set API response scenario.
        $this->response['scenario'] = 'confirmation.success';
      }
      // User registered more than 8 hours ago.
      else{
        // Generate new confirmation token.
        $user->generateconfirmationToken();
        // Send confirmation email.
        Mail::to($user->email)->send(new EmailConfirmation($user->email_confirmation_token));
        // Save changes.
        $user->save();
        // Set API response code.
        $this->code = 410;
        // Set API response scenario.
        $this->response['scenario'] = 'confirmation.failed.expired';
      }
    }

    // Return the response.
    return response()->json($this->response, $this->code);
  }

  /**
   * Login.
   */
  public function login(UserLoginRequest $request){
    // Login credentials.
    $credentials = [
      // Used for case insensitive validation.
      'email' => strtolower($request->email),
      'password' => $request->password,
    ];

    // Succusseful login.
    if (Auth::attempt($credentials)) {
      // User is verified.
      if(auth()->user()->email_confirmed){
        // Set API success code.
        $this->code = 200;
        // Set API response scenario.
        $this->response['scenario'] = 'login.success';
        // Set and store session token.
        $this->response['data']['token'] = auth()->user()->createToken('API Token')->plainTextToken;
        // Set user infomrmation.
        $this->response['data']['user']['id'] = auth()->id();
        $this->response['data']['user']['name'] = auth()->user()->name;
        $this->response['data']['clothes'] = auth()->user()->clothes;
        $this->response['data']['days'] = Day::with('clothes')->where('user_id', auth()->user()->id)->get();
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
  * Logout.
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

  /**
   * Send forgotten password.
   */
  public function sendForgottenPassword(SendForgottenPasswordRequest $request){
    
    // Get wether or not User is confirmed.
    $verified = User::where(['email' => $request->email])->first()->email_confirmed;
    // User is not confirmed.
    if($verified === '0'){
      // Set API response code 409 - Conflict.
      $this->code = 409;
      // Set API response scenario.
      $this->response['scenario'] = 'forgotten.failed.not-confirmed';
      // Send API response.
      return response()->json($this->response, $this->code);
    }
    
    // Set and send forgotten password token.
    $this->setAndSendForgottenPasswordToken($request->email);

    // Set API response code.
    $this->code = 200;
    // Set API response scenario.
    $this->response['scenario'] = 'forgotten.success.sent';
    

    // Send API response.
    return response()->json($this->response, $this->code);
  }

  /**
   * Verify forgotten password token.
   */
  public function verifyForgottenPasswordToken(VerifyForgottenPasswordTokenRequest $request){
    // If validitation under VerifyForgottenPasswordTokenRequest passed, this method also passes.
    // Set API response scenario.
    $this->response['scenario'] = 'forgotten.success.token';
    // Set API response code.
    $this->code = 200;
    // Send API response.
    return response()->json($this->response, $this->code);
  }

  /**
   * Change password through forgotten password.
   */
  public function changePasswordThroughForgottenPassword(ChangePasswordThroughForgottenPasswordRequest $request){
    // Get row from password_reset table.
    $row = DB::table('password_resets')->where('token', $request->token)->first();

    // Forgotten password request was made less than 8 hours ago.
    if(Carbon::create($row->created_at)->diffInHours() < 8){
      // Get User id using the token.
      $email = $row->email;
      // Get User data.
      $user = User::where('email', $email)->first();
      // Change password.
      $user->password = Hash::make($request->password);
      // Save changes.
      $user->save();
      // Delete token DB entry.
      DB::table('password_resets')->where('token', $request->token)->delete();
      // Set API response code.
      $this->code = 200;
      // Set API response scenario.
      $this->response['scenario'] = 'forgotten.success.changed';
    }
    // Forgotten password request was made 8 or more hours ago.
    else{
      // Set and send forgotten password token.
      $this->setAndSendForgottenPasswordToken($row->email);
      // Set API response code.
      $this->code = 410;
      // Set API response scenario.
      $this->response['scenario'] = 'forgotten.failed.expired';
    }
    

    // Send API response.
    return response()->json($this->response, $this->code);
  }
}