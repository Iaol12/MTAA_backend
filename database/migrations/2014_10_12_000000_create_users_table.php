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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255);
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('password', 100);
            $table->unsignedBigInteger('zlava_id')->nullable();
            $table->unsignedBigInteger('user_role');
        
            $table->foreign('zlava_id')->references('id')->on('discounts');
            $table->foreign('user_role')->references('id')->on('user_roles');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
