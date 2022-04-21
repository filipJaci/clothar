<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyForgottenPasswordRequest extends FormRequest{

  // In case of failed validation.
  protected function failedValidation(Validator $validator) {
    // API response code 400 - Bad request.
    $code = 400;
    // Scenario used in API response.
    $scenario = null;
    // Password confirmation failed.
    if($validator->messages()->has('password')){
        // Set appropriate scenario.
        $scenario = 'forgotten-password.failed.password';
        // Change API reponse code to 422 - Invalid data.
        $code = 422;
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
      ], $code)
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
      'password' => [
        'string',
        'required',
        'min:8',
        'max:40',
        'regex:/[a-z]/',      // must contain at least one lowercase letter
        'regex:/[A-Z]/',      // must contain at least one uppercase letter
        'regex:/[0-9]/',      // must contain at least one digit
        'regex:/[@$!%*#?&]/', // must contain a special character
      ],
    ];
  }
}
