<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Modifichiamo la colonna esistente per aggiungere il nuovo ruolo
            $table->enum('role', ['customer', 'worker', 'superadmin'])->default('customer')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Se facciamo un rollback, torniamo alla situazione precedente
            $table->enum('role', ['customer', 'worker'])->default('customer')->change();
        });
    }
};
