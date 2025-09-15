<?php

// in database/migrations/xxxx_xx_xx_xxxxxx_add_role_to_users_table.php

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
        Schema::table('users', function (Blueprint $table) {
            // Aggiungiamo la nostra colonna 'role' dopo la colonna 'email'
            $table->enum('role', ['customer', 'worker'])->default('customer')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rimuoviamo la colonna se facciamo un rollback della migrazione
            $table->dropColumn('role');
        });
    }
};