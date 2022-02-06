<?php

namespace Database\Factories;

use App\Models\Day;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User;

class DayFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Day::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
      return [
        'date' => $this->faker->dateTimeThisMonth()->format('Y-m-d H:i:s'),
        'user_id' => function(){ return User::factory()->create()->id; }
      ];
    }
}
