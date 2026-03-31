<?php
namespace App\Http\Controllers;
// ── Importer les modèles ─────────────────────────────────────────────
use App\Models\User;
use App\Models\Patient;
// ── Importer les facades Laravel ─────────────────────────────────────────────
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ── Afficher le formulaire de connexion ──────────────────────────
    public function showLogin() {
        return view('auth.login');
    }

    // ── Traiter la connexion ─────────────────────────────────────────
    public function login(Request $request) {
        // Validation des champs
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Tentative de connexion
        if (Auth::attempt([
            'email'    => $request->email,
            'password' => $request->password
        ])) {
            /** @var \App\Models\User $user */
                $user = Auth::user();

            // Redirection selon le rôle
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isMedecin()) {
                return redirect()->route('medecin.dashboard');
            } elseif ($user->isSecretaire()) {
                return redirect()->route('secretaire.dashboard');
            } elseif ($user->isPatient()) {
                return redirect()->route('patient.dashboard');
            }
        }

        // Echec de connexion
        return back()->withErrors([
            'email' => 'Email ou mot de passe incorrect.',
        ]);
    }

    // ── Afficher le formulaire d'inscription ─────────────────────────
    public function showRegister() {
        return view('auth.register');
    }

    // ── Traiter l'inscription ────────────────────────────────────────
    public function register(Request $request) {
        // Validation des champs
        $request->validate([
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'email'          => 'required|email|unique:users',
            'password'       => 'required|min:8|confirmed',
            'telephone'      => 'nullable|string|max:20',
            'date_naissance' => 'nullable|date',
            'adresse'        => 'nullable|string|max:255',
        ]);

        // Créer le User
        $user = User::create([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'telephone' => $request->telephone,
            'role'      => 'patient',
        ]);

        // Créer le profil Patient associé
        Patient::create([
            'id_user'        => $user->id,
            'date_naissance' => $request->date_naissance,
            'adresse'        => $request->adresse,
        ]);

        // Connecter automatiquement après inscription
        Auth::login($user);

        return redirect()->route('patient.dashboard');
    }

    // ── Déconnexion ──────────────────────────────────────────────────
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}