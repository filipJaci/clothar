<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Carbon\Exceptions\InvalidFormatException;

use App\Models\Day;
use App\Models\Cloth;

class DayTest extends TestCase
{
    private function createClothAndGetId(){

        return Cloth::create([
            'title' => 'Short Sleeves shirt',
            'description' => null,
            'category' => null,
            'buy_at' => null,
            'buy_date' => null,
            'status' => 1,
        ])->id;

    }

    private $message = '';

    use RefreshDatabase;

    /** @test */
    public function cloth_foreign_key_constraint_test()
    {

        try
        {
            Day::create([
                'date' => '2021-12-25',
                'cloth_id' => 1,
                'ocassion' => 1
            ]);
        }

        catch(QueryException $e)
        {
            $this->message = $e->errorInfo[2];
        }

        $this->assertEquals('FOREIGN KEY constraint failed', $this->message);
    }

    /** @test */
    public function date_has_to_be_in_a_correct_format()
    {

        try
        {
            Day::create([
                'date' => '20211-20-25',
                'cloth_id' => $this->createClothAndGetId(),
                'ocassion' => 1
            ]);
        }

        catch(InvalidFormatException $e)
        {
            $this->assertInstanceOf(InvalidFormatException::class, $e);;
        }

    }
}
