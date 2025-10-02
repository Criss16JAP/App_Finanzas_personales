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
    Schema::create('card_purchases', function (Blueprint $table) {
        $table->id();
        $table->foreignId('credit_card_id')->constrained()->onDelete('cascade');
        $table->string('description');
        $table->decimal('purchase_amount', 15, 2);
        $table->unsignedInteger('installments');
        $table->unsignedInteger('installments_paid')->default(0);
        $table->decimal('remaining_balance', 15, 2);
        $table->date('purchase_date');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_purchases');
    }
};
