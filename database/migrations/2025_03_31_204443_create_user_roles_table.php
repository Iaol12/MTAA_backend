<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 255);
            $table->integer('privilege');
        });       
        DB::table('user_roles')->insert([
            ['id' => 1, 'role_name' => 'User', 'privilege' => 1],
            ['id' => 2,'role_name' => 'Admin', 'privilege' => 2],
        ]); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
