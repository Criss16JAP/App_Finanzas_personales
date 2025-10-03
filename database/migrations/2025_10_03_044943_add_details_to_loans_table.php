<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('interest_rate', 5, 4)->default(0.0000)->after('borrower_name');
            $table->integer('term_months')->default(1)->after('interest_rate');
            $table->integer('payment_day_of_month')->default(1)->after('term_months');
            $table->decimal('accrued_interest_balance', 15, 2)->default(0.00)->after('paid_amount');
            $table->date('last_interest_accrued_on')->nullable()->after('payment_day_of_month');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'interest_rate',
                'term_months',
                'payment_day_of_month',
                'accrued_interest_balance',
                'last_interest_accrued_on'
            ]);
        });
    }
};
