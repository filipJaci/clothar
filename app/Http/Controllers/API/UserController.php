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
  public function register(Request $request){

    // successful register
    try {
      // make a new User instance
      $user = new User();
      // set field values
      $user->name = $request->name;
      $user->email = $request->email;
      $user->password = Hash::make($request->password);
      // save User
      $user->save();

      // set API response code
      $this->code = 200;

      // set API response messages
      $this->response['title'] = 'Registration successful';
      $this->response['message'] = 'You may Login now.';
    }
    // failed register
    catch (\Illuminate\Database\QueryException $ex) {

      if($ex->errorInfo[2] === 'UNIQUE constraint failed: users.email'){
        // set API response code
        // 409 - Conflict - indication that changing credentials would help
        $this->code = 409;
        // set API response messages
        $this->response['title'] = 'Registration failed';
        $this->response['message'] = 'This email is already registered.';
      }
      else{
        // set API response code
        // 400 - Bad request - broader than conflict
        $this->code = 400;
        $this->response['title'] = 'Registration failed';
        $this->response['message'] = 'Unknown error.';
      }

    }

    // return response
    return response()->json($this->response, $this->code);
  }

  /**
   * Login
   */
  public function login(Request $request){

    // set API response title
    $this->response['title'] = 'Login attempt';

    // login credentials
    $credentials = [
      'email' => $request->email,
      'password' => $request->password,
    ];

    // succusseful login
    if (Auth::attempt($credentials)) {
      // set API success code
      $this->code = 200;
      // set API response message
      $this->response['message'] = 'Login successful.';
      // set and store session token
      $this->response['data']['token'] = auth()->user()->createToken('API Token')->plainTextToken;
      // set user infomrmation
      $this->response['data']['user']['id'] = auth()->id();
      $this->response['data']['user']['name'] = auth()->user()->name;
      $this->response['data']['clothes'] = Cloth::all();
      $this->response['data']['days'] = Day::with('clothes')->get();
    }
    
    // failed login
    else {
      // set API unaothorized code
      $this->code = 401;
      // set API response message
      $this->response['message'] = 'Login failed.';
    }
    
    // send API response
    return response()->json($this->response, $this->code);
  }

  /**
  * Logout
  */
  public function logout(){
    // set API response title
    $this->response['title'] = 'Logout attempt';

    // attempt to logout
    try {
      // drop session
      Session::flush();
      // set API success code
      $this->code = 200;
      // set API response message
      $this->response['message'] = 'Logout successful.';
    }
    
    catch (\Illuminate\Database\QueryException $ex) {
      // set API response code
      // 400 - Bad request - broader than conflict
      $this->code = 400;
      // set API response message
      $this->response['message'] = 'Logout failed: - ' . $ex->getMessage();
    }

    // send API response
    return response()->json($this->response, $this->code);
  }
}