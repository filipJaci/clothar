<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Cloth;

class ClothTest extends TestCase
{

    private function storeCloth(){

        return $this->post('/cloth',[
            'image' => 'image.jpg',
            'description' => null,
            'category' => null,
            'buy_at' => null,
            'buy_date' => null,
            // IN USE
            'status' => 1,
        ]);

    }

    use RefreshDatabase;

    /** @test */
    public function a_piece_of_cloth_can_be_added()
    {
        $this->withoutExceptionHandling();

        $response = $this->storeCloth();

        $response->assertOk();
        $this->assertCount(1, Cloth::all());
    }

     /** @test */
     public function a_piece_of_cloth_has_image()
     {
 
        $response = $this->post('/cloth',[
            'image' => null,
            'description' => null,
            'category' => null,
            'buy_at' => null,
            'buy_date' => null,
            // IN USE
            'status' => 1,
        ]);
 
        $response->assertSessionHasErrors('image');

     }

     /** @test */
     public function a_piece_of_cloth_has_status()
     {
 
        $response = $this->post('/cloth',[
            'image' => 'image.jpg',
            'description' => null,
            'category' => null,
            'buy_at' => null,
            'buy_date' => null,
            // IN USE
            'status' => null,
        ]);
 
         $response->assertSessionHasErrors('status');
     }

     /** @test */
     public function a_piece_of_cloth_can_be_updated()
     {

        $cloth = $this->storeCloth();

        $this->patch('/cloth/' . Cloth::first()->id, [
            'image' => 'new_image.jpg',
            'description' => "new description",
            'category' => 1,
            'buy_at' => "ACME Store",
            'buy_date' => "10.10.2020.",
            // IN USE
            'status' => 1,
        ]);

        $cloth = Cloth::first();

        $this->assertEquals('new_image.jpg', $cloth->image);
        $this->assertEquals('new description', $cloth->description);
        $this->assertEquals(1, $cloth->category);
        $this->assertEquals('ACME Store', $cloth->buy_at);
        $this->assertEquals('10.10.2020.', $cloth->buy_date);
        $this->assertEquals(1, $cloth->status);
     }
     
}
