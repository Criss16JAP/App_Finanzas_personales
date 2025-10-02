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
    Schema::create('credit_payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('credit_id')->constrained()->onDelete('cascade');
        $table->foreignId('movement_id')->constrained()->onDelete('cascade');
        $table->decimal('amount_paid', 15, 2);
        $table->decimal('principal_paid', 15, 2);
        $table->decimal('interest_paid', 15, 2);
        $table->date('payment_date');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_payments');
    }
};
