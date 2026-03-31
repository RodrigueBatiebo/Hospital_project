<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medecin extends User
{
    protected $table = 'medecins';

    protected $fillable = [
        'id_user',
        'specialite',
        'numero_ordre',
    ];

     // Un médecin peut avoir plusieurs rendez-vous
    public function rendezVous() {
        return $this->hasMany(RendezVous::class, 'id_medecin');
    }

    // Un médecin peut avoir plusieurs disponibilités
    public function disponibilites() {
        return $this->hasMany(Disponibilite::class, 'id_medecin');
    }

    // Un médecin peut appartenir à plusieurs services
    public function services() {
        return $this->belongsToMany(Service::class, 'medecin_service', 'id_medecin', 'id_service');
    }
}
