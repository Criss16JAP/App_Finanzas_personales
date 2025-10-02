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
        Schema::table('credits', function (Blueprint $table) {
            $table->decimal('fixed_monthly_fee', 15, 2)->default(0.00)->after('interest_rate');
        });

        Schema::table('credit_payments', function (Blueprint $table) {
            $table->decimal('fee_paid', 15, 2)->default(0.00)->after('interest_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->dropColumn('fixed_monthly_fee');
        });

        Schema::table('credit_payments', function (Blueprint $table) {
            $table->dropColumn('fee_paid');
        });
    }
};
