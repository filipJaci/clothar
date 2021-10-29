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
            'image' => 'image.jpg',
            'description' => null,
            'category' => null,
            'buyAt' => null,
            'buyDate' => null,
            // IN USE
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
     public function a_piece_of_cloth_has_image()
     {
 
        $response = $this->post('/cloth',[
            'image' => null,
            'description' => null,
            'category' => null,
            'buyAt' => null,
            'buyDate' => null,
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
            'buyAt' => null,
            'buyDate' => null,
            // IN USE
            'status' => null,
        ]);
 
         $response->assertSessionHasErrors('status');
     }

     /** @test */
     public function a_piece_of_cloth_can_be_updated()
     {
        $this->withoutExceptionHandling();

        $cloth = $this->insertCloth();

        $this->patch('/cloth/' . Cloth::first()->id, [
            'image' => 'new_image.jpg',
            'description' => "new description",
            'category' => 1,
            'buyAt' => "ACME Store",
            'buyDate' => "2020-10-20",
            // IN USE
            'status' => 1,
        ]);

        $cloth = Cloth::first();

        $this->assertEquals('new_image.jpg', $cloth->image);
        $this->assertEquals('new description', $cloth->description);
        $this->assertEquals(1, $cloth->category);
        $this->assertEquals('ACME Store', $cloth->buyAt);
        $this->assertEquals(Carbon::parse('2020-10-20'), $cloth->buyDate);
        $this->assertEquals(1, $cloth->status);

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
