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
    Schema::create('credit_cards', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->decimal('credit_limit', 15, 2);
        $table->decimal('interest_rate', 5, 4); // Ej: 0.0184 para 1.84%
        $table->decimal('monthly_fee', 15, 2)->default(0.00);
        $table->unsignedTinyInteger('cutoff_day'); // Día del mes (1-31)
        $table->unsignedTinyInteger('payment_day'); // Día del mes (1-31)
        $table->decimal('current_debt', 15, 2)->default(0.00);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_cards');
    }
};
