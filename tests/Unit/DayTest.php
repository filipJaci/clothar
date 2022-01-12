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

        return Cloth::factory()->create()->id;

    }

    private $message = '';

    use RefreshDatabase;

    /** @test */
    public function the_date_field_is_required(){

        try
        {
            Day::create([
                'date' => null,
            ]);
        }

        catch(QueryException $e)
        {
            $this->message =$e->errorInfo[2];
        }

        $this->assertEquals('NOT NULL constraint failed: days.date', $this->message);
    }

    /** @test */
    public function date_has_to_be_in_a_correct_format()
    {

        try
        {
            Day::create([
                'date' => '20211-20-25',
            ]);
        }

        catch(InvalidFormatException $e)
        {
            $this->assertInstanceOf(InvalidFormatException::class, $e);;
        }

    }

    /** @test */
    public function if_a_day_is_removed_all_clothes_that_were_worn_on_that_day_are_removed_as_well()
    {
        $day = Day::factory()->create();

        $cloth = Cloth::factory()->create();

        $day->clothes()->attach([$cloth->id => ['ocassion' => 1]]);

        $this->assertEquals(1, $cloth->days()->count());

        $day->delete();

        $this->assertEquals(0, $cloth->days()->count());
    }
}
