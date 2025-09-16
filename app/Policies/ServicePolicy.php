<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    /**
     * Determina se l'utente può aggiornare il modello.
     */
    public function update(User $user, Service $service): bool
    {
        // L'utente può aggiornare il servizio se è il proprietario O se è un superadmin.
        return $user->id === $service->user_id || $user->role === 'superadmin';
    }

    // Qui in futuro potrai aggiungere altre regole (create, delete, etc.)
}
