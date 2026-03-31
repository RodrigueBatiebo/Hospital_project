<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Secretaire extends User
{
    protected $table = 'secretaires';

    protected $fillable = [
        'id_user',
        'id_service',
    ];
    // Une secrétaire appartient à un seul service
    public function service() {
        return $this->belongsTo(Service::class, 'id_service');
    }

    // Une secrétaire peut gérer plusieurs rendez-vous
    public function rendezVous() {
        return $this->hasMany(RendezVous::class, 'id_secretaire');
    }
}
