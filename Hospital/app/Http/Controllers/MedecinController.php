<?php
namespace App\Http\Controllers;

use App\Models\Disponibilite;
use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedecinController extends Controller
{
    // ── Afficher le dashboard médecin ─────────────────────────────────
    public function dashboard() {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $medecin = $user->medecin;

        // Rendez-vous du jour
        $rdvDuJour = RendezVous::where('id_medecin', $medecin->id)
            ->where('date', today())
            ->where('statut', 'valide')
            ->orderBy('heure', 'asc')
            ->get();

        // Disponibilités à venir
        $disponibilites = Disponibilite::where('id_medecin', $medecin->id)
            ->where('date', '>=', today())
            ->orderBy('date', 'asc')
            ->get();

        return view('medecin.dashboard', compact('rdvDuJour', 'disponibilites'));
    }

    // ── Afficher le planning complet ──────────────────────────────────
    public function planning() {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $medecin = $user->medecin;

        $rendezVous = RendezVous::where('id_medecin', $medecin->id)
            ->where('statut', 'valide')
            ->orderBy('date', 'asc')
            ->orderBy('heure', 'asc')
            ->get();

        return view('medecin.planning', compact('rendezVous'));
    }

    // ── Afficher l'historique des rendez-vous ─────────────────────────
    public function historique() {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $medecin = $user->medecin;

        $historique = RendezVous::where('id_medecin', $medecin->id)
            ->where('date', '<', today())
            ->orderBy('date', 'desc')
            ->get();

        return view('medecin.historique', compact('historique'));
    }

    // ── Afficher les disponibilités ───────────────────────────────────
    public function disponibilites() {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $medecin = $user->medecin;

        $disponibilites = Disponibilite::where('id_medecin', $medecin->id)
            ->orderBy('date', 'asc')
            ->get();

        return view('medecin.disponibilites.index', compact('disponibilites'));
    }

    // ── Ajouter une disponibilité ─────────────────────────────────────
    public function storeDisponibilite(Request $request) {
        $request->validate([
            'date'        => 'required|date|after_or_equal:today',
            'heure_debut' => 'required',
            'heure_fin'   => 'required|after:heure_debut',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $medecin = $user->medecin;

        Disponibilite::create([
            'id_medecin'  => $medecin->id,
            'date'        => $request->date,
            'heure_debut' => $request->heure_debut,
            'heure_fin'   => $request->heure_fin,
            'statut'      => 'disponible',
        ]);

        return back()->with('success', 'Disponibilité ajoutée avec succès.');
    }

    // ── Modifier une disponibilité ────────────────────────────────────
    public function updateDisponibilite(Request $request, $id) {
        $request->validate([
            'date'        => 'required|date',
            'heure_debut' => 'required',
            'heure_fin'   => 'required|after:heure_debut',
        ]);

        $disponibilite = Disponibilite::findOrFail($id);
        $disponibilite->update([
            'date'        => $request->date,
            'heure_debut' => $request->heure_debut,
            'heure_fin'   => $request->heure_fin,
        ]);

        return back()->with('success', 'Disponibilité modifiée avec succès.');
    }

    // ── Supprimer une disponibilité ───────────────────────────────────
    public function destroyDisponibilite($id) {
        $disponibilite = Disponibilite::findOrFail($id);
        $disponibilite->delete();

        return back()->with('success', 'Disponibilité supprimée avec succès.');
    }
}