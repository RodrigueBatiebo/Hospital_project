<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SecretaireController;
use App\Http\Controllers\MedecinController;
use App\Http\Controllers\PatientController;

//----routes secretaire---------------------------
Route::middleware(['auth'])->prefix('secretaire')->group(function () {
    Route::get('/dashboard',                [SecretaireController::class, 'dashboard'])->name('secretaire.dashboard');
    Route::get('/rendezvous',               [SecretaireController::class, 'index'])->name('secretaire.rendezvous');
    Route::post('/rendezvous/store',        [SecretaireController::class, 'store'])->name('secretaire.store');
    Route::post('/rendezvous/{id}/valider', [SecretaireController::class, 'valider'])->name('secretaire.valider');
    Route::post('/rendezvous/{id}/refuser', [SecretaireController::class, 'refuser'])->name('secretaire.refuser');
    Route::post('/rendezvous/{id}/reprogrammer', [SecretaireController::class, 'reprogrammer'])->name('secretaire.reprogrammer');
});

//-----routes authentification-------------------

// ── Routes publiques (sans authentification) ─────────────────────────
Route::get('/',          [AuthController::class, 'showLogin'])->name('home');
#Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',    [AuthController::class, 'login'])->name('login.submit');
Route::get('/register',  [AuthController::class, 'showRegister'])->name('inscription');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// ── Routes protégées (avec authentification) ─────────────────────────
Route::middleware(['auth'])->group(function () {

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboards par rôle
    Route::get('/admin/dashboard',       fn() => view('admin.dashboard'))->name('admin.dashboard');
    Route::get('/medecin/dashboard',     fn() => view('medecin.dashboard'))->name('medecin.dashboard');
    #Route::get('/secretaire/dashboard',  fn() => view('secretaire.dashboard'))->name('secretaire.dashboard');
    #Route::get('/patient/dashboard',     fn() => view('patient.dashboard'))->name('patient.dashboard');

});


//-----routes medecin-------------------


Route::middleware(['auth'])->prefix('medecin')->group(function () {
    Route::get('/dashboard',                        [MedecinController::class, 'dashboard'])->name('medecin.dashboard');
    Route::get('/planning',                         [MedecinController::class, 'planning'])->name('medecin.planning');
    Route::get('/historique',                       [MedecinController::class, 'historique'])->name('medecin.historique');
    Route::get('/disponibilites',                   [MedecinController::class, 'disponibilites'])->name('medecin.disponibilites');
    Route::post('/disponibilites/store',            [MedecinController::class, 'storeDisponibilite'])->name('medecin.disponibilites.store');
    Route::post('/disponibilites/{id}/update',      [MedecinController::class, 'updateDisponibilite'])->name('medecin.disponibilites.update');
    Route::delete('/disponibilites/{id}/destroy',   [MedecinController::class, 'destroyDisponibilite'])->name('medecin.disponibilites.destroy');
});

//-----routes patient-------------------
#Route::get('/dashboardp',[PatientController::class, 'dashboard'])->name('patient.index');
#Route::get('/dashboardp',[PatientController::class, 'dashboard'])->name('patient.index');



Route::middleware(['auth'])->prefix('patient')->group(function () {

    // Dashboard
    Route::get('/dashboard',
        [PatientController::class, 'dashboard'])->name('patient.dashboard');

    // Liste de tous les RDV
    Route::get('/rendezvous',
        [PatientController::class, 'index'])->name('patient.rendezvous');

    // Formulaire prise de RDV
    Route::get('/rendezvous/create',
        [PatientController::class, 'create'])->name('patient.rendezvous.create');

    // Enregistrer le RDV
    Route::post('/rendezvous/store',
        [PatientController::class, 'store'])->name('patient.rendezvous.store');

    // Annuler un RDV
    Route::post('/rendezvous/{id}/annuler',
        [PatientController::class, 'annuler'])->name('patient.rendezvous.annuler');

    // Récupérer les médecins d'un service (AJAX)
    Route::get('/medecins/{id_service}',
        [PatientController::class, 'getMedecins'])->name('patient.medecins');

    // Récupérer les disponibilités d'un médecin (AJAX)
    Route::get('/disponibilites/{id_medecin}',
        [PatientController::class, 'getDisponibilites'])->name('patient.disponibilites');

    // Notifications
    Route::get('/notifications',
        [PatientController::class, 'notifications'])->name('patient.notifications');

    // Marquer toutes les notifications comme lues
    Route::post('/notifications/lire',
        [PatientController::class, 'marquerLu'])->name('patient.notifications.lire');
});