<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyForgottenPasswordRequest extends FormRequest{

  // In case of failed validation.
  protected function failedValidation(Validator $validator) {
    // Scenario used in API response.
    $scenario = null;
    // Password confirmation failed.
    if($validator->messages()->has('password')){
        // Set appropriate scenario.
        $scenario = 'forgotten-password.failed.password';
    }
    // Token doesn't exist.
    else if($validator->messages()->has('token')){
        // Set appropriate scenario.
        $scenario = 'forgotten-password.failed.token';
    }

    throw new HttpResponseException(
      response()->json([
        'scenario' => $scenario,
        'data' => $validator->errors()->all()
      // Response HTTP status code is 400 - Bad request.
      ], 400)
    );
  }

  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize(){
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(){

    return [
     'token' => 'required|exists:password_resets,token',
     'password' => 'required|confirmed',
    ];
  }
}
