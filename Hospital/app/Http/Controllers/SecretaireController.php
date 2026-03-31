<?php
namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecretaireController extends Controller
{
    // ── Afficher le dashboard secrétaire ─────────────────────────────
    public function dashboard() {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $secretaire = $user->secretaire;

        // Récupérer les rendez-vous du service de la secrétaire
        $rendezVous = RendezVous::where('id_service', $secretaire->id_service)
            ->where('statut', 'en_attente')
            ->orderBy('date', 'asc')
            ->get();

        return view('secretaire.dashboard', compact('rendezVous'));
    }

    // ── Afficher tous les rendez-vous du service ──────────────────────
    public function index() {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $secretaire = $user->secretaire;

        $rendezVous = RendezVous::where('id_service', $secretaire->id_service)
            ->orderBy('date', 'asc')
            ->get();

        return view('secretaire.rendezvous.index', compact('rendezVous'));
    }

    // ── Valider un rendez-vous ────────────────────────────────────────
    public function valider($id) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $secretaire = $user->secretaire;

        $rdv = RendezVous::findOrFail($id);
        $rdv->statut       = 'valide';
        $rdv->id_secretaire = $secretaire->id;
        $rdv->save();

        // Envoyer une notification au patient
        Notification::create([
            'id_destinataire' => $rdv->patient->id_user,
            'id_rendezvous'   => $rdv->id,
            'message'         => 'Votre rendez-vous du ' . $rdv->date . ' à ' . $rdv->heure . ' a été validé.',
            'type'            => 'confirmation',
            'date_envoi'      => now(),
        ]);

        return back()->with('success', 'Rendez-vous validé avec succès.');
    }

    // ── Refuser un rendez-vous ────────────────────────────────────────
    public function refuser(Request $request, $id) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $secretaire = $user->secretaire;

        $rdv = RendezVous::findOrFail($id);
        $rdv->statut        = 'refuse';
        $rdv->id_secretaire = $secretaire->id;
        $rdv->save();

        // Envoyer une notification au patient
        Notification::create([
            'id_destinataire' => $rdv->patient->id_user,
            'id_rendezvous'   => $rdv->id,
            'message'         => 'Votre rendez-vous du ' . $rdv->date . ' à ' . $rdv->heure . ' a été refusé.',
            'type'            => 'refus',
            'date_envoi'      => now(),
        ]);

        return back()->with('error', 'Rendez-vous refusé.');
    }

    // ── Reprogrammer un rendez-vous ───────────────────────────────────
    public function reprogrammer(Request $request, $id) {
        $request->validate([
            'date'  => 'required|date',
            'heure' => 'required',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $secretaire = $user->secretaire;

        $rdv = RendezVous::findOrFail($id);
        $rdv->date          = $request->date;
        $rdv->heure         = $request->heure;
        $rdv->statut        = 'valide';
        $rdv->id_secretaire = $secretaire->id;
        $rdv->save();

        // Envoyer une notification au patient
        Notification::create([
            'id_destinataire' => $rdv->patient->id_user,
            'id_rendezvous'   => $rdv->id,
            'message'         => 'Votre rendez-vous a été reprogrammé au ' . $rdv->date . ' à ' . $rdv->heure . '.',
            'type'            => 'reprogrammation',
            'date_envoi'      => now(),
        ]);

        return back()->with('success', 'Rendez-vous reprogrammé avec succès.');
    }

    // ── Ajouter manuellement un rendez-vous ───────────────────────────
    public function store(Request $request) {
        $request->validate([
            'id_patient'  => 'required|exists:patients,id',
            'id_medecin'  => 'required|exists:medecins,id',
            'id_service'  => 'required|exists:services,id',
            'date'        => 'required|date',
            'heure'       => 'required',
            'motif'       => 'nullable|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $secretaire = $user->secretaire;

        RendezVous::create([
            'id_patient'    => $request->id_patient,
            'id_medecin'    => $request->id_medecin,
            'id_secretaire' => $secretaire->id,
            'id_service'    => $request->id_service,
            'date'          => $request->date,
            'heure'         => $request->heure,
            'statut'        => 'valide',
            'motif'         => $request->motif,
        ]);

        return back()->with('success', 'Rendez-vous ajouté avec succès.');
    }
}