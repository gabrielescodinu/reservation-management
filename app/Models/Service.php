<?php

// in app/Models/Service.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importa BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany; // Importa HasMany

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'duration',
    ];

    /**
     * Definisce la relazione: un servizio appartiene a un utente (worker).
     */
    public function worker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Un servizio può avere molte prenotazioni.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
