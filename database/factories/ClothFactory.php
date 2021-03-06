<?php

namespace Database\Factories;

use App\Models\Cloth;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

use App\Models\User;

class ClothFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Cloth::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition(){
    return [
      'title' => $this->faker->name(),
      'description' => $this->faker->text(),
      'category' => 1,
      'buy_at' => $this->faker->text(),
      'buy_date' => '2021-11-14',
      'status' => 1,
      'user_id' => function(){ return User::factory()->create()->id; }
    ];
  }
}
