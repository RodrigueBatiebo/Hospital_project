<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends User
{
    protected $table = 'patients';

    protected $fillable = [
        'id_user',
        'date_naissance',
        'adresse',
    ];
        // Récupère le compte User associé à ce patient s'il n'y avait as d'heritage
    /*public function user() {
        return $this->belongsTo(User::class, 'id_user');
    }*/

    // Un patient peut avoir plusieurs rendez-vous
    public function rendezVous() {
        return $this->hasMany(RendezVous::class, 'id_patient');
    }

}
