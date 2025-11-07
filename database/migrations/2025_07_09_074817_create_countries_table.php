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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('name')->unique();
            $table->string('symbol');
            $table->smallInteger('decimal')->default(2)->nullable();
            $table->string('group_separator', 10)->default(',')->nullable();
            $table->string('decimal_separator', 10)->default('.')->nullable();
            $table->enum('currency_position', ['Left', 'Right']);
            $table->string('symbol_html');
            $table->boolean('is_default')->default(false);
            $table->decimal('exchange_rate', 10, 4)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->decimal('percentage', 8, 2);
            $table->boolean('is_active');
            $table->string('stripe_id')->nullable();
            $table->timestamps();
        });

        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2)->unique();
            $table->string('name');
            $table->integer('currency_id');
            $table->string('icon');
            $table->integer('tax_id');
            $table->timestamps();
        });

        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->integer('country_id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->integer('province_id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->integer('city_id');
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('areas');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('currencies');
    }
};
