<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

use App\Models\Day;
use App\Models\Cloth;

class ClothDayManagmentTest extends TestCase
{

    private function createClothAndGetId(){

        return Cloth::factory()->create()->id;

    }

    private function createDayWithCloth(){

        return $this->post('api/days', [
            'date' => '2021-12-12',
            'clothes' => [$this->createClothAndGetId()],
            // 'ocassion' => 1
        ]);

    }

    private function checkResponseFormat($response){

        $this->assertArrayHasKey('title', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('write', $response);
        $this->assertArrayHasKey('data', $response);

    }

    use RefreshDatabase;

    /** @test */
    public function cloth_and_day_can_be_added(){

        $this->withoutExceptionHandling();

        $response = $this->createDayWithCloth();
        
        $response->assertOk();
        $this->checkResponseFormat($response);

        $this->assertCount(1, Day::all());
        $this->assertCount(1, Cloth::all());

        $this->assertEquals(1, Cloth::first()->days()->count());

    }

    /** @test */
    public function cloth_and_day_update_syncs_database(){
        
        $this->withoutExceptionHandling();

        $this->createDayWithCloth();

        $day = Day::first();

        $newClothId = $this->createClothAndGetId();

        $response = $this->post('api/days', [
            'date' => $day->date,
            'clothes' => [$newClothId],
            'ocassion' => 1
        ]);
        
        $this->checkResponseFormat($response);

        $this->assertEquals(1, $day->clothes()->count());
        $this->assertEquals($newClothId, $day->clothes()->first()->id);

    }

    /** @test */
    public function cloth_and_day_index_retrives_days_with_clothes(){

        $this->withoutExceptionHandling();
        
        $this->createDayWithCloth();
        $this->createDayWithCloth();

        $response = $this->get('api/days/');

        $response->assertOk();
        $this->checkResponseFormat($response);

        $this->assertCount(2, $response['data']);
        $this->assertArrayHasKey('clothes', $response['data'][0]);
    
    }

    /** @test */
    public function a_day_without_clothes_is_removed_from_the_database(){
        
        $this->withoutExceptionHandling();
        
        $this->createDayWithCloth();

        $this->assertEquals(1, Cloth::with('days')->first()->days->count());

        $dayId = Day::first()->id;

        $response = $this->delete('api/days/' . $dayId);

        $response->assertOk();
        $this->checkResponseFormat($response);

        $this->assertEquals(0, Cloth::with('days')->first()->days->count());
    
    }
}
