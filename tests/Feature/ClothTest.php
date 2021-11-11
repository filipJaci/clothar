<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Cloth;
use Carbon\Carbon;

class ClothTest extends TestCase
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
     public function a_piece_of_cloth_cannot_be_added_without_title()
     {
        // $this->withoutExceptionHandling();
 
        $response = $this->post('/cloth',[
            'title' => null,
            'description' => null,
            'category' => null,
            'buy_at' => null,
            'buy_date' => null,
            'status' => null,
        ]);

        $response->assertSessionHasErrors('title');
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
     
}
