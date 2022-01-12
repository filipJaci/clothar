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
    public function a_piece_of_cloth_cannot_be_added_without_title()
    {

        try
        {
            Cloth::create([
                'title' => null,
                'description' => null,
                'category' => null,
                'buy_at' => null,
                'buy_date' => null,
                'status' => null,
            ]);
        }
        
        catch(QueryException $e)
        {
            $this->message = $e->errorInfo[2];
        }

        $this->assertEquals('NOT NULL constraint failed: clothes.title', $this->message);
    }

    /** @test */
    public function a_day_can_be_accessed_through_eloquent_relationship()
    {
        $cloth = Cloth::factory()->create();

        $dayId = Day::factory()->create()->id;

        $cloth->days()->attach([$dayId => ['ocassion' => 1]]);

        $this->assertInstanceOf(Day::class, $cloth->days()->first());
    }

    /** @test */
    public function if_a_cloth_is_removed_all_days_that_cloth_was_worn_on_are_removed_as_well(){

        $cloth = Cloth::factory()->create();

        $day = Day::factory()->create();

        $cloth->days()->attach([$day->id => ['ocassion' => 1]]);

        $this->assertEquals(1, $day->clothes()->count());

        $cloth->delete();

        $this->assertEquals(0, $day->clothes()->count());

    }
}
