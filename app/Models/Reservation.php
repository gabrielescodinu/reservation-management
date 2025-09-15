<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'service_id',
        'start_time',
        'end_time',
        'status',
    ];

    /**
     * Una prenotazione appartiene a un cliente (User).
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Una prenotazione è per un servizio specifico.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
