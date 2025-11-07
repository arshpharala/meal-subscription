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
            $table->enum('status', ['active', 'paused', 'cancelled', 'payment_failed'])->default('active')->after('stripe_status');
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
        });
    }
};
