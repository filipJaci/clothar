<?php

namespace App\Http\Controllers\API;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Cloth;
use App\Models\Day;

use App\Http\Requests\UserRegistrationRequest;
use App\Http\Requests\UserLoginRequest;

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

    // Successful register.
    try {
      // Make a new User instance.
      $user = new User();
      // Set field values.
      $user->name = $request->name;
      $user->email = $request->email;
      $user->password = Hash::make($request->password);
      // Save User.
      $user->save();

      // Set API response code
      $this->code = 200;

      // Set API response messages.
      $this->response['title'] = 'Registration successful';
      $this->response['message'] = 'You may Login now.';
    }
    // failed register
    catch (\Illuminate\Database\QueryException $ex) {

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