<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'nom_service',
        'description',
    ];

    // Un service peut avoir plusieurs médecins
    public function medecins() {
        return $this->belongsToMany(Medecin::class, 'medecin_service', 'id_service', 'id_medecin');
    }

    // Un service peut avoir une secrétaire
    public function secretaires() {
        return $this->hasMany(Secretaire::class, 'id_service');
    }

    // Un service peut avoir plusieurs rendez-vous
    public function rendezVous() {
        return $this->hasMany(RendezVous::class, 'id_service');
    }
}