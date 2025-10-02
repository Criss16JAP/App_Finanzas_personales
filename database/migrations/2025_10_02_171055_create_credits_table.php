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
    Schema::create('credits', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('principal_amount', 15, 2);
        $table->decimal('interest_rate', 5, 4); // Para guardar tasas como 0.0150 (1.5%)
        $table->integer('term_months');
        $table->decimal('current_balance', 15, 2);
        $table->date('issued_date');
        $table->integer('payment_day_of_month'); // Nuevo campo: día del mes para el pago
        $table->date('last_interest_accrued_on')->nullable(); // Nuevo campo: control de intereses
        $table->string('status')->default('active'); // active, paid
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
