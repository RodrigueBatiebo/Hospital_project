<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';

    protected $fillable = [
        'id_patient',
        'id_medecin',
        'id_secretaire',
        'id_service',
        'date',
        'heure',
        'statut',
        'motif',
    ];

    // Un rendez-vous appartient à un seul patient
    public function patient() {
        return $this->belongsTo(Patient::class, 'id_patient');
    }

    // Un rendez-vous appartient à un seul médecin
    public function medecin() {
        return $this->belongsTo(Medecin::class, 'id_medecin');
    }

    // Un rendez-vous est géré par une seule secrétaire
    public function secretaire() {
        return $this->belongsTo(Secretaire::class, 'id_secretaire');
    }

    // Un rendez-vous concerne un seul service
    public function service() {
        return $this->belongsTo(Service::class, 'id_service');
    }

    // Un rendez-vous peut générer plusieurs notifications
    public function notifications() {
        return $this->hasMany(Notification::class, 'id_rendezvous');
    }
}