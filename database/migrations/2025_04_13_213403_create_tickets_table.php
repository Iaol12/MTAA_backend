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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('train_id');
            $table->unsignedBigInteger('start_station');
            $table->unsignedBigInteger('end_station');
            $table->timestamp('platny_od')->nullable();
            $table->timestamp('platny_do')->nullable();
            $table->string('qr_token', 255)->comment('lepsie ako ked qr je id listku');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('train_id')->references('id')->on('trains');
            $table->foreign('start_station')->references('id')->on('stations');
            $table->foreign('end_station')->references('id')->on('stations');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
