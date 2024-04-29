<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('job_title');
            $table->string('phone');
            $table->date('birthdate');
            $table->string('cv')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('password'); // Hashed
            $table->enum('role', ['admin', 'user'])->default('user'); // Default role is 'user'
            $table->timestamps();
        });
        // Creating an admin user
        DB::table('users')->insert([
            'username' => env('ADMIN_USERNAME', 'admin'),
            'email' => env('ADMIN_EMAIL', 'admin@example.com'),
            'first_name' => env('ADMIN_FIRST_NAME', 'Admin'),
            'last_name' => env('ADMIN_LAST_NAME', 'Admin'),
            'job_title' =>env('ADMIN_JOB_TITLE', 'Admin'),
            'phone' => env('ADMIN_PHONE', '1234567890'),
            'birthdate' => env('ADMIN_BIRTHDATE', '1990-01-01'),
            'cv' => env('ADMIN_CV', null),
            'profile_picture' => env('ADMIN_PROFILE_PICTURE', null),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
            'role' => 'admin',
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
