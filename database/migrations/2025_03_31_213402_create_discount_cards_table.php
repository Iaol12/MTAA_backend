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
        Schema::create('discount_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('zlava_id');
            $table->integer('zlavovy_kod');
            $table->timestamps();

            $table->foreign('zlava_id')->references('id')->on('discounts');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_cards');
    }
};
