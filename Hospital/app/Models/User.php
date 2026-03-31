<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['nom','prenom', 'email', 'password','telephone','role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    // ── Vérifications du rôle ───────────────────────────────────────
    public function isAdmin(): bool {
        return $this->role === 'admin';
    }

    public function isMedecin(): bool {
        return $this->role === 'medecin';
    }

    public function isSecretaire(): bool {
        return $this->role === 'secretaire';
    }

    public function isPatient(): bool {
        return $this->role === 'patient';
    }
    //les relations
    // Un User possède un seul profil Patient
    // Clé étrangère : id_user dans la table patients
    public function patient() {
        return $this->hasOne(Patient::class, 'id_user');
    }

    // Un User possède un seul profil Médecin
    // Clé étrangère : id_user dans la table medecins
    public function medecin() {
        return $this->hasOne(Medecin::class, 'id_user');
    }

    // Un User possède un seul profil Secrétaire
    // Clé étrangère : id_user dans la table secretaires
    public function secretaire() {
        return $this->hasOne(Secretaire::class, 'id_user');
    }
}
