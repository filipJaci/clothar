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
        return $this->post('api/days', [
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

        $response = $this->patch('api/days/' . Day::first()->id, [
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

        $this->delete('api/days/' . Day::first()->id);

        $this->assertCount(0, Day::all());
    }

    /** @test */
    public function a_day_can_be_shown()
    {
        $this->withoutExceptionHandling();

        $this->createDay();

        $response = $this->get('api/days/' . Day::first()->id);

        $response->assertOk();

        $this->assertArrayHasKey('title', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('write', $response);
        $this->assertArrayHasKey('data', $response);

        $this->assertArrayHasKey('id', $response['data']);
        $this->assertArrayHasKey('created_at', $response['data']);
        $this->assertArrayHasKey('updated_at', $response['data']);

        $this->assertArrayHasKey('cloth_id', $response['data']);
        $this->assertInstanceOf(Cloth::class, Cloth::find($response['data']['id']));

        $this->assertArrayHasKey('ocassion', $response['data']);
    
    }
}
