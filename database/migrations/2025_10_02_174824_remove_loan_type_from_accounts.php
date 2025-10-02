<?php

use App\Models\Account;
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
        // 1. Eliminar todas las cuentas existentes de tipo 'loan'
        Account::where('type', 'loan')->delete();

        // 2. (Opcional pero recomendado) Modificar la columna para restringir los valores
        // Esto previene que se puedan crear cuentas de tipo 'loan' en el futuro a nivel de BD
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('type')->comment("bank, cash, credit_card")->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // El método down no puede restaurar los datos eliminados,
        // pero podemos revertir el cambio en la columna.
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('type')->comment("")->change();
        });
    }
};
