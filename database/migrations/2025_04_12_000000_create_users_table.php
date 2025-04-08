<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('password', 100);
            $table->unsignedBigInteger('zlava_id')->nullable();
            $table->unsignedBigInteger('user_role')->default(1);
            $table->timestamps();
            
            $table->foreign('zlava_id')->references('id')->on('discounts');
            $table->foreign('user_role')->references('id')->on('user_roles');
        });
        DB::table('users')->insert([
            'email' => 'admin@example.com',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'password' => Hash::make('admin123'), // Securely hash the password
            'zlava_id' => null, // No discount for admin
            'user_role' => 2, // Admin role
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
