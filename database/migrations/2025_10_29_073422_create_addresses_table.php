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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->enum('type', ['work', 'home', 'other'])->default('other');
            $table->string('phone');
            $table->integer('country_id')->default(1);
            $table->integer('province_id');
            $table->integer('city_id');
            $table->integer('area_id');
            $table->string('address');
            $table->string('landmark')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
