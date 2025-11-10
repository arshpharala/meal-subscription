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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignUuid('meal_package_id')->nullable()->after('user_id');
            $table->foreignUuid('meal_package_price_id')->nullable()->after('meal_package_id');
            $table->date('next_charge_date')->nullable()->after('ends_at');
            $table->boolean('auto_charge')->default(true)->after('next_charge_date'); // to control jobs
            $table->enum('status', ['scheduled', 'active', 'paused', 'cancelled', 'payment_failed'])->default('active')->after('stripe_status');
            $table->double('sub_total');
            $table->double('tax_amount');
            $table->double('total');
            $table->integer('currency_id');
            $table->text('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->removeColumn('meal_package_id');
            $table->removeColumn('meal_package_price_id');
            $table->removeColumn('next_charge_date');
            $table->removeColumn('auto_charge');
            $table->removeColumn('status');
            $table->removeColumn('sub_total');
            $table->removeColumn('tax_amount');
            $table->removeColumn('total');
            $table->removeColumn('currency_id');
            $table->removeColumn('description');
        });
    }
};
