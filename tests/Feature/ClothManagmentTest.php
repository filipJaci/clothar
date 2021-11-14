<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Cloth;
use Carbon\Carbon;

class ClothManagmentTest extends TestCase
{

    private function insertCloth(){

        return $this->post('/cloth',[
            'title' => 'Short Sleeves shirt',
            'description' => null,
            'category' => null,
            'buy_at' => null,
            'buy_date' => null,
            'status' => 1,
        ]);

    }

    use RefreshDatabase;

    /** @test */
    public function a_piece_of_cloth_can_be_added()
    {
        $this->withoutExceptionHandling();

        $response = $this->insertCloth();

        $response->assertOk();
        $this->assertCount(1, Cloth::all());
    }



     /** @test */
     public function a_piece_of_cloth_can_be_updated()
     {
        $this->withoutExceptionHandling();

        $cloth = $this->insertCloth();

        $this->patch('/cloth/' . Cloth::first()->id, [
            'title' => 'Long Sleves shirt',
            'description' => "new description",
            'category' => 1,
            'buy_at' => "ACME Store",
            'buy_date' => "2020-10-20",
            'status' => 2,
        ]);

        $cloth = Cloth::first();

        $this->assertEquals('Long Sleves shirt', $cloth->title);
        $this->assertEquals('new description', $cloth->description);
        $this->assertEquals(1, $cloth->category);
        $this->assertEquals('ACME Store', $cloth->buy_at);
        $this->assertEquals(Carbon::parse('2020-10-20'), $cloth->buy_date);
        $this->assertEquals(2, $cloth->status);

     }

     /** @test */
     public function a_piece_of_cloth_can_be_removed()
     {
        $this->withoutExceptionHandling();

        $this->insertCloth();

        $response = $this->delete('/cloth/' . Cloth::first()->id);
        
        $this->assertCount(0, Cloth::all());

     }


     /** @test */
     public function a_piece_of_cloth_can_be_showed()
     {
        $this->withoutExceptionHandling();

        $this->insertCloth();

        $response = $this->get('/cloth/' . Cloth::first()->id);

        $response->assertOk();

        $this->assertArrayHasKey('title', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('write', $response);
        $this->assertArrayHasKey('data', $response);

        $this->assertArrayHasKey('id', $response['data']);
        $this->assertArrayHasKey('created_at', $response['data']);
        $this->assertArrayHasKey('updated_at', $response['data']);
        $this->assertArrayHasKey('status', $response['data']);
        $this->assertArrayHasKey('title', $response['data']);
        $this->assertArrayHasKey('description', $response['data']);
        $this->assertArrayHasKey('category', $response['data']);
        $this->assertArrayHasKey('buy_at', $response['data']);
        $this->assertArrayHasKey('buy_date', $response['data']);
        $this->assertArrayHasKey('status', $response['data']);

     }
     
}
