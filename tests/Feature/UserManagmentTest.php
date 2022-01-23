<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\user;

class UserManagmentTest extends TestCase
{

  use RefreshDatabase;

  // User data
  private $userData = [
    'name' => 'John Doe',
    'email' => 'john.doe@mail.com',
    'password' => 'password'
  ];

  // registers a User
  private function registerUser(){
    return $this->post('api/register', $this->userData);
  }

  private function loginUser(){
    // register a user
    $this->registerUser();

    // record a response
    // login as a User
    return $response = $this->post('api/login', [
      'email' => $this->userData['email'],
      'password' => $this->userData['password'],
    ]);
  }

  // checks api repsonse format, wheter or not all keys are present
  private function checkResponseFormat($response){
    $this->assertArrayHasKey('title', $response);
    $this->assertArrayHasKey('message', $response);
    $this->assertArrayHasKey('write', $response);
    $this->assertArrayHasKey('data', $response);

  }
  
  /** @test */
  public function a_user_can_register(){

    // record a response
    // register a user
    $response = $this->registerUser();

    // response HTTP status code is ok
    $response->assertOk();
    // check response format
    $this->checkResponseFormat($response);

    // there is 1 User in the DB
    $this->assertCount(1, User::all());
  }

  /** @test */
  public function a_user_can_login(){
    // record response
    // login User
    $response = $this->loginUser();
    
    // response HTTP status code is ok
    $response->assertOk();
    // check response format
    $this->checkResponseFormat($response);

    // check data response format
    // token is present
    $this->assertArrayHasKey('token', $response['data']);
    // user data is present
    $this->assertArrayHasKey('user', $response['data']);
    // user data has all the valid keys
    $this->assertArrayHasKey('id', $response['data']['user']);
    $this->assertArrayHasKey('name', $response['data']['user']);
    $this->assertArrayHasKey('clothes', $response['data']);
    $this->assertArrayHasKey('days', $response['data']);

  }

  /** @test */
  public function a_logged_in_user_can_make_a_request_only_a_logged_in_user_should_be_able_to(){
    // login user
    $this->loginUser();
    // record a response
    // get Clothes
    $response = $this->getJSON('api/clothes');
    
    // response HTTP status code is ok
    $response->assertOk();
    // check response format
    $this->checkResponseFormat($response);

  }

  /** @test */
  public function a_user_can_logout(){

    // log in a User
    $this->loginUser();

    // record a response
    // logout as a User
    $response = $this->postJson('api/logout');

    // response HTTP status code is ok
    $response->assertOk();
    // check response format
    $this->checkResponseFormat($response);
    
  }
}
