<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disponibilite extends Model
{
    use HasFactory;

    protected $table = 'disponibilites';

    protected $fillable = [
        'id_medecin',
        'date',
        'heure_debut',
        'heure_fin',
        'statut',
    ];

    // Une disponibilité appartient à un seul médecin
    public function medecin() {
        return $this->belongsTo(Medecin::class, 'id_medecin');
    }
}