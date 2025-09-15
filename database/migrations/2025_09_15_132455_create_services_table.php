<?php

// in database/migrations/xxxx_xx_xx_xxxxxx_create_services_table.php

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
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            // Relazione con l'utente (worker)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Campi specifici del servizio
            $table->string('name');
            $table->text('description')->nullable(); // La descrizione può essere opzionale
            $table->decimal('price', 8, 2); // 8 cifre totali, 2 decimali (es. 999999.99)
            $table->integer('duration'); // Durata in minuti

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};