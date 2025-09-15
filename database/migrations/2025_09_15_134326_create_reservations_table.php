<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            // Chi sta prenotando? (il customer)
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');

            // Quale servizio viene prenotato?
            $table->foreignId('service_id')->constrained()->onDelete('cascade');

            // Quando inizia la prenotazione?
            $table->dateTime('start_time');

            // Quando finisce? Lo calcoliamo, ma è utile averlo nel DB
            $table->dateTime('end_time');

            // Stato della prenotazione (per future espansioni)
            $table->enum('status', ['confirmed', 'cancelled'])->default('confirmed');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
