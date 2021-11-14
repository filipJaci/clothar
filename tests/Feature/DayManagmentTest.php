<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

use App\Models\Day;
use App\Models\Cloth;

class DayManagmentTest extends TestCase
{

    private function createClothAndGetId(){

        return Cloth::factory()->create()->id;

    }

    private function createDay()
    {
        return $this->post('/day', [
            'date' => '2021-12-12',
            'cloth_id' => $this->createClothAndGetId(),
            'ocassion' => 1
        ]);
    }

    use RefreshDatabase;

    /** @test */
    public function a_day_can_be_added()
    {
        $this->withoutExceptionHandling();

        $response = $this->createDay();
        
        $response->assertOk();
        $this->assertCount(1, Day::all());
    }

    /** @test */
    public function a_day_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $this->createDay();

        $newClothId = $this->createClothAndGetId();

        $response = $this->patch('/day/' . Day::first()->id, [
            'date' => '2022-01-01',
            'cloth_id' => $newClothId,
            'ocassion' => 2
        ]);

        $response->assertOk();

        $updatedDay = Day::first();

        $this->assertEquals(Carbon::parse('2022-01-01'), $updatedDay->date);
        $this->assertEquals($newClothId, $updatedDay->cloth_id);
        $this->assertEquals(2, $updatedDay->ocassion);

    }
    
    /** @test */
    public function a_day_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        $this->createDay();

        $dayId = Day::first()->id;

        $this->delete('/day/' . $dayId);

        $this->assertCount(0, Day::all());
    }
}
