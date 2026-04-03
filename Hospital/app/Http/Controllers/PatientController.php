<?php
namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\Disponibilite;
use App\Models\Service;
use App\Models\Medecin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    // ── Afficher le dashboard patient ─────────────────────────────────
    public function dashboard() {
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $patient = $user->patient;

        // Prochains rendez-vous
        $prochainRdv = RendezVous::where('id_patient', $patient->id)
            ->where('date', '>=', today())
            ->where('statut', 'valide')
            ->orderBy('date', 'asc')
            ->get();

        // Rendez-vous en attente
        $rdvEnAttente = RendezVous::where('id_patient', $patient->id)
            ->where('statut', 'en_attente')
            ->orderBy('date', 'asc')
            ->get();
        $rendezVous = RendezVous::where('id_patient', $patient->id)
            ->orderBy('date', 'desc')
            ->get();
        $stats = [
        'en_attente' => $rendezVous->where('statut', 'en_attente')->count(),
        'valide'     => $rendezVous->where('statut', 'valide')->count(),
        'refuse'     => $rendezVous->where('statut', 'refuse')->count(),
        'total'      => $rendezVous->count(),
            ];

        return view('patient.dashboard', compact('prochainRdv', 'rdvEnAttente','stats'));
    }

    // ── Afficher le formulaire de prise de rendez-vous ────────────────
    public function create() {
        // Récupérer tous les services disponibles
        $services = Service::all();

        return view('patient.rendezvous.create', compact('services'));
    }

    // ── Récupérer les médecins d'un service ───────────────────────────
    public function getMedecins($id_service) {
        $medecins = Medecin::whereHas('services', function ($query) use ($id_service) {
            $query->where('services.id', $id_service);
        })->get();

        return response()->json($medecins);
    }

    // ── Récupérer les disponibilités d'un médecin ─────────────────────
    public function getDisponibilites($id_medecin) {
        $disponibilites = Disponibilite::where('id_medecin', $id_medecin)
            ->where('statut', 'disponible')
            ->where('date', '>=', today())
            ->orderBy('date', 'asc')
            ->get();

        return response()->json($disponibilites);
    }

    // ── Enregistrer un rendez-vous ────────────────────────────────────
    public function store(Request $request) {
        $request->validate([
            'id_service'      => 'required|exists:services,id',
            'id_medecin'      => 'required|exists:medecins,id',
            'id_disponibilite'=> 'required|exists:disponibilites,id',
            'motif'           => 'nullable|string|max:255',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $patient = $user->patient;

        // Récupérer la disponibilité choisie
        $disponibilite = Disponibilite::findOrFail($request->id_disponibilite);

        // Créer le rendez-vous
        RendezVous::create([
            'id_patient'  => $patient->id,
            'id_medecin'  => $request->id_medecin,
            'id_service'  => $request->id_service,
            'date'        => $disponibilite->date,
            'heure'       => $disponibilite->heure_debut,
            'statut'      => 'en_attente',
            'motif'       => $request->motif,
        ]);

        // Marquer la disponibilité comme occupée
        $disponibilite->update(['statut' => 'occupe']);

        return redirect()->route('patient.dashboard')
            ->with('success', 'Rendez-vous pris avec succès, en attente de validation.');
    }

    // ── Afficher tous les rendez-vous du patient ──────────────────────
    public function index() {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $patient = $user->patient;

        $rendezVous = RendezVous::where('id_patient', $patient->id)
            ->orderBy('date', 'desc')
            ->paginate(5);

        return view('patient.rendezvous.index', compact('rendezVous'));
    }

    // ── Annuler un rendez-vous ────────────────────────────────────────
    public function annuler($id) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $patient = $user->patient;

        $rdv = RendezVous::where('id', $id)
            ->where('id_patient', $patient->id)
            ->firstOrFail();

        // Annuler le rendez-vous
        $rdv->update(['statut' => 'annule']);

        // Libérer la disponibilité du médecin
        Disponibilite::where('id_medecin', $rdv->id_medecin)
            ->where('date', $rdv->date)
            ->where('heure_debut', $rdv->heure)
            ->update(['statut' => 'disponible']);

        return back()->with('success', 'Rendez-vous annulé avec succès.');
    }
}
