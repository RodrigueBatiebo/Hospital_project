<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'id_destinataire',
        'id_rendezvous',
        'message',
        'type',
        'date_envoi',
    ];

    // Une notification appartient à un seul utilisateur
    public function destinataire() {
        return $this->belongsTo(User::class, 'id_destinataire');
    }

    // Une notification est liée à un seul rendez-vous
    public function rendezVous() {
        return $this->belongsTo(RendezVous::class, 'id_rendezvous');
    }
}