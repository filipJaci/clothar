<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;

use App\Models\Cloth;

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
}
