<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Service;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Le prenotazioni che un utente ha fatto come cliente.
     */
    public function reservationsAsCustomer(): HasMany
    {
        return $this->hasMany(Reservation::class, 'customer_id');
    }

    /**
     * Le prenotazioni associate a un utente come worker, attraverso i suoi servizi.
     * Questa è una relazione "Has Many Through".
     */
    public function reservationsAsWorker(): HasManyThrough
    {
        return $this->hasManyThrough(Reservation::class, Service::class);
    }
}
