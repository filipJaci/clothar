<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Cloth;
use App\Models\Day;

class ClothDay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloth_day', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Cloth::class)->constrained('clothes')->onDelete('cascade');
            $table->foreignIdFor(Day::class)->constrained()->onDelete('cascade');
            $table->integer('ocassion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
