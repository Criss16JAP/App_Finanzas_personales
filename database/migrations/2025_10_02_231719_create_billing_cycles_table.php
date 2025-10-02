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
    Schema::create('billing_cycles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('credit_card_id')->constrained()->onDelete('cascade');
        $table->date('statement_date');
        $table->date('due_date');
        $table->decimal('total_installments_due', 15, 2);
        $table->decimal('interest_charged', 15, 2)->default(0.00);
        $table->decimal('fees_charged', 15, 2)->default(0.00);
        $table->decimal('closing_balance', 15, 2);
        $table->decimal('amount_paid', 15, 2)->default(0.00);
        $table->string('status')->default('pending'); // pending, paid, partially_paid
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_cycles');
    }
};
