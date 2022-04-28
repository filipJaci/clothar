<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
      return [
        // Makes sure that there is a required symbol in the name.
        'name' => $this->faker->unique()->name(),
        'email' => $this->faker->unique()->safeEmail(),
        'email_confirmed' => false,
        'email_confirmation_token' => Str::random(10),
        'password' => '$2y$10$XTAQlp7pQ6fGtYr4j1eQv.9pTfDdB7lC4VJS3ly.4nmwjqDnA1M7O', // User1234!
        'remember_token' => Str::random(10),
      ];
    }

    // /**
    //  * Indicate that the model's email address should be unverified.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Factories\Factory
    //  */
    // public function unverified(){
    //   return $this->state(function (array $attributes) {
    //     return [
    //       'email_verified_at' => null,
    //     ];
    //   });
    // }
}
