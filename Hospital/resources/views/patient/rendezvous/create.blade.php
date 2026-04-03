@extends('patient.layouts.app')

@section('title', 'Prendre un rendez-vous')

@section('content')

<div class="page-header">
    <h1>Prendre un rendez-vous</h1>
</div>

{{-- ── Étapes ─────────────────────────────────────────────── --}}
<div class="steps">
    <div class="step active">
        <div class="step-num">1</div>
        <span>Service</span>
    </div>
    <div class="step-sep"></div>
    <div class="step {{ old('id_service') ? 'active' : '' }}">
        <div class="step-num">2</div>
        <span>Médecin</span>
    </div>
    <div class="step-sep"></div>
    <div class="step {{ old('id_disponibilite') ? 'active' : '' }}">
        <div class="step-num">3</div>
        <span>Créneau</span>
    </div>
    <div class="step-sep"></div>
    <div class="step">
        <div class="step-num">4</div>
        <span>Confirmation</span>
    </div>
</div>

<div style="max-width:540px">
    <div class="card">
        <form action="{{ route('patient.rendezvous.store') }}" method="POST" id="form-rdv">
            @csrf

            {{-- Service --}}
            <div class="form-group">
                <label>Service médical</label>
                <select name="id_service"
                        id="sel-service"
                        class="form-control"
                        required
                        onchange="loadMedecins(this.value)">
                    <option value="">-- Choisir un service --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}"
                                {{ old('id_service') == $service->id ? 'selected' : '' }}>
                            {{ $service->nom_service }}
                        </option>
                    @endforeach
                </select>
                @error('id_service')
                    <span style="color:#d9534f;font-size:12px">{{ $message }}</span>
                @enderror
            </div>

            {{-- Médecin --}}
            <div class="form-group">
                <label>Médecin</label>
                <select name="id_medecin"
                        id="sel-medecin"
                        class="form-control"
                        required
                        onchange="loadDisponibilites(this.value)"
                        {{ !old('id_service') ? 'disabled' : '' }}>
                    <option value="">-- Choisir d'abord un service --</option>
                    @if(old('id_service'))
                        @foreach($medecins as $m)
                            <option value="{{ $m->id }}" {{ old('id_medecin') == $m->id ? 'selected' : '' }}>
                                Dr. {{ $m->user->prenom }} {{ $m->user->nom }} — {{ $m->user->specialite }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('id_medecin')
                    <span style="color:#d9534f;font-size:12px">{{ $message }}</span>
                @enderror
            </div>

            {{-- Créneau --}}
            <div class="form-group">
                <label>Créneau disponible</label>
                <select name="id_disponibilite"
                        id="sel-dispo"
                        class="form-control"
                        required
                        {{ !old('id_medecin') ? 'disabled' : '' }}>
                    <option value="">-- Choisir d'abord un médecin --</option>
                    @if(old('id_medecin'))
                        @foreach($disponibilites as $d)
                            <option value="{{ $d->id }}" {{ old('id_disponibilite') == $d->id ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($d->date)->translatedFormat('d F Y') }}
                                — {{ \Carbon\Carbon::parse($d->heure_debut)->format('H\hi') }}
                                à {{ \Carbon\Carbon::parse($d->heure_fin)->format('H\hi') }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('id_disponibilite')
                    <span style="color:#d9534f;font-size:12px">{{ $message }}</span>
                @enderror
            </div>

            {{-- Motif --}}
            <div class="form-group">
                <label>
                    Motif de la consultation
                    <span style="color:#aaa;font-size:12px">(optionnel)</span>
                </label>
                <textarea name="motif"
                          class="form-control"
                          rows="3"
                          placeholder="Décrivez brièvement votre motif de consultation...">{{ old('motif') }}</textarea>
            </div>

            {{-- Boutons --}}
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
                <a href="{{ route('patient.dashboard') }}" class="btn">Annuler</a>
                <button type="submit" class="btn btn-primary">Confirmer le rendez-vous</button>
            </div>
        </form>
    </div>
</div>

<script>
function loadMedecins(serviceId) {
    const selMedecin = document.getElementById('sel-medecin');
    const selDispo   = document.getElementById('sel-dispo');

    if (!serviceId) {
        selMedecin.innerHTML = '<option value="">-- Choisir d\'abord un service --</option>';
        selMedecin.disabled  = true;
        selDispo.innerHTML   = '<option value="">-- Choisir d\'abord un médecin --</option>';
        selDispo.disabled    = true;
        return;
    }

    selMedecin.innerHTML = '<option value="">Chargement...</option>';
    selMedecin.disabled  = true;

    fetch(`/patient/medecins/${serviceId}`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        selMedecin.innerHTML = '<option value="">-- Choisir un médecin --</option>';
        data.forEach(m => {
            selMedecin.innerHTML +=
                `<option value="${m.id}">Dr. ${m.prenom} ${m.nom} — ${m.specialite}</option>`;
        });
        selMedecin.disabled = data.length === 0;
        if (data.length === 0) {
            selMedecin.innerHTML = '<option value="">Aucun médecin disponible</option>';
        }
    })
    .catch(() => {
        selMedecin.innerHTML = '<option value="">Erreur de chargement</option>';
    });
}

function loadDisponibilites(medecinId) {
    const selDispo = document.getElementById('sel-dispo');

    if (!medecinId) {
        selDispo.innerHTML = '<option value="">-- Choisir d\'abord un médecin --</option>';
        selDispo.disabled  = true;
        return;
    }

    selDispo.innerHTML = '<option value="">Chargement...</option>';
    selDispo.disabled  = true;

    fetch(`/patient/disponibilites/${medecinId}`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        selDispo.innerHTML = '<option value="">-- Choisir un créneau --</option>';
        data.forEach(d => {
            const date  = new Date(d.date).toLocaleDateString('fr-FR', {day:'2-digit',month:'long',year:'numeric'});
            const debut = d.heure_debut.substring(0, 5).replace(':', 'h');
            const fin   = d.heure_fin.substring(0, 5).replace(':', 'h');
            selDispo.innerHTML += `<option value="${d.id}">${date} — ${debut} à ${fin}</option>`;
        });
        selDispo.disabled = data.length === 0;
        if (data.length === 0) {
            selDispo.innerHTML = '<option value="">Aucun créneau disponible</option>';
        }
    })
    .catch(() => {
        selDispo.innerHTML = '<option value="">Erreur de chargement</option>';
    });
}
</script>

@endsection
