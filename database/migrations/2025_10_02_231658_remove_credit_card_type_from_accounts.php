<?php

use App\Models\Account;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar cualquier cuenta existente de este tipo
        Account::where('type', 'credit_card')->delete();

        // Actualizar la validación en la vista para que ya no aparezca
        // (Esto ya lo hicimos manualmente en el archivo accounts/index.blade.php)
    }

    public function down(): void
    {
        // Esta acción no se puede revertir, los datos se eliminan.
    }
};
