<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyForgottenPasswordTokenRequest extends FormRequest{

  // In case of failed validation.
  protected function failedValidation(Validator $validator) {
    // API response code 400 - Bad request.
    $code = 400;
    // Scenario used in API response.
    $scenario = null;
    // Token doesn't exist.
    // Set appropriate scenario.
    $scenario = 'forgotten.failed.token';

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
      'token' => 'required|exists:password_resets,token'
    ];
  }

  /**
   * Prepare the data for validation.
   *
   * @return void
   */
  protected function prepareForValidation()
  {
    $this->merge([
        'token' => $this->route()->parameters()['token'],
    ]);
  }
}
