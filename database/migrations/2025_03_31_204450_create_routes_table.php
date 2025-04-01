<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->unsignedBigInteger('train_id');
            $table->unsignedBigInteger('station_id');
            $table->integer('sequence_number');
            $table->timestamp('departure_time')->nullable();
        
            $table->foreign('train_id')->references('id')->on('trains');
            $table->foreign('station_id')->references('id')->on('stations');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
