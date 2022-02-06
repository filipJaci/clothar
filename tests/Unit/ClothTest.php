<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;

use App\Models\Cloth;
use App\Models\Day;

class ClothTest extends TestCase
{
  private $message;

  use RefreshDatabase;

  /** @test */
  public function a_piece_of_cloth_cannot_be_added_without_title(){
    // attempt to
    try{
      // create a Cloth
      Cloth::create([
        // without title
        'title' => null,
        'description' => null,
        'category' => null,
        'buy_at' => null,
        'buy_date' => null,
        'status' => null,
      ]);
    }

    // If creation fails:
    // Catch error as QueryException.
    catch(QueryException $e){
      // Get eror message out of error.
      $this->message = $e->errorInfo[2];
    }
    // Check if it's the correct error message.
    $this->assertEquals('NOT NULL constraint failed: clothes.title', $this->message);
  }

  /** @test */
  public function a_day_can_be_accessed_through_eloquent_relationship(){
    // Create a new Cloth and store response.
    $cloth = Cloth::factory()->create();
    // Create a new Day and store its id.
    $dayId = Day::factory()->create()->id;
    // Attach Cloth to Day.
    // Set ocassion manually, will be used later on.
    $cloth->days()->attach([$dayId => ['ocassion' => 1]]);
    // A Day can be fetched through Cloth.
    $this->assertInstanceOf(Day::class, $cloth->days()->first());
  }
}
