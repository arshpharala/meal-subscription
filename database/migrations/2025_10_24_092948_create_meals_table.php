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
        Schema::create('meals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->string('position')->default(99);
            $table->boolean('is_active')->default(true);
            $table->decimal('starting_price', 10, 2)->nullable();
            $table->string('sample_menu_file')->nullable();
            $table->enum('color', ['success', 'warning', 'primary', 'danger', 'secondary'])->default('secondary');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('tagline')->nullable();
            $table->string('thumbnail')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('meal_packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('meal_id');
            $table->uuid('package_id');
            $table->string('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('stripe_product_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('calories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('label');          // "800-1000", "1050-1200"
            $table->unsignedInteger('min_kcal')->nullable();
            $table->unsignedInteger('max_kcal')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('meal_package_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('meal_package_id');
            $table->uuid('calorie_id');
            $table->integer('duration')->default(30);
            $table->uuid('extra_charges_id')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('stripe_price_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['meal_package_id', 'calorie_id', 'duration'], 'unique_meal');
        });

        Schema::create('extra_charges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');                 // "Cooler Bag Deposit"
            $table->string('type');                 // "Daily, On Time, Per Meal"
            $table->decimal('amount', 10, 2);       // 100.00
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meals');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('meal_packages');
        Schema::dropIfExists('calories');
        Schema::dropIfExists('meal_package_prices');
        Schema::dropIfExists('extra_charges');
    }
};
