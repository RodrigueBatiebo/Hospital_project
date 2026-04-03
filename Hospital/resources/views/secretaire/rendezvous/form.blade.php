@extends('secretaire.layouts.app')

@section('title', isset($rdv) ? 'Reprogrammer RDV' : 'Ajouter RDV')

@section('content')

<div class="page-header">
    <h1>{{ isset($rdv) ? 'Reprogrammer le rendez-vous' : 'Ajouter un rendez-vous manuellement' }}</h1>
</div>

<div style="max-width:540px">
    <div class="card">
        <form action="{{ isset($rdv) ? route('secretaire.reprogrammer', $rdv->id) : route('secretaire.store') }}"
              method="POST">
            @csrf

            @unless(isset($rdv))
            {{-- Patient --}}
            <div class="form-group">
                <label>Patient</label>
                <select name="id_patient" class="form-control" required>
                    <option value="">Sélectionner un patient</option>
                    @foreach($patients  as $patient)
                        <option value="{{ $patient->id }}" {{ old('id_patient') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->prenom }} {{ $patient->nom }}
                        </option>
                    @endforeach
                </select>
                @error('id_patient')
                    <span style="color:#d9534f;font-size:12px">{{ $message }}</span>
                @enderror
            </div>
            @endunless

            @if(isset($rdv))
            <div class="card" style="background:#f8f8f8;margin-bottom:16px">
                <p style="font-size:13px;color:#555">
                    <strong>Patient :</strong> {{ $rdv->patient->prenom }} {{ $rdv->patient->nom }}<br>
                    <strong>Médecin :</strong> Dr. {{ $rdv->medecin->prenom }} {{ $rdv->medecin->nom }}<br>
                    <strong>Motif :</strong> {{ $rdv->motif ?? '—' }}
                </p>
            </div>
            @endif

            @unless(isset($rdv))
            {{-- Médecin --}}
            <div class="form-group">
                <label>Médecin</label>
                <select name="id_medecin" class="form-control" required>
                    <option value="">Sélectionner un médecin</option>
                    @foreach($medecins as $medecin)
                        <option value="{{ $medecin->id }}" {{ old('id_medecin') == $medecin->id ? 'selected' : '' }}>
                            Dr. {{ $medecin->prenom }} {{ $medecin->nom }} — {{ $medecin->specialite }}
                        </option>
                    @endforeach
                </select>
                @error('id_medecin')
                    <span style="color:#d9534f;font-size:12px">{{ $message }}</span>
                @enderror
            </div>

            {{-- Service --}}
            <div class="form-group">
                <label>Service</label>
                <select name="id_service" class="form-control" required>
                    <option value="">Sélectionner un service</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('id_service') == $service->id ? 'selected' : '' }}>
                            {{ $service->nom_service }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endunless

            {{-- Date et heure --}}
            <div class="form-row cols-2">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date"
                           name="date"
                           class="form-control"
                           value="{{ old('date', isset($rdv) ? $rdv->date : '') }}"
                           min="{{ date('Y-m-d') }}"
                           required>
                    @error('date')
                        <span style="color:#d9534f;font-size:12px">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Heure</label>
                    <input type="time"
                           name="heure"
                           class="form-control"
                           value="{{ old('heure', isset($rdv) ? \Carbon\Carbon::parse($rdv->heure)->format('H:i') : '') }}"
                           required>
                    @error('heure')
                        <span style="color:#d9534f;font-size:12px">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            @unless(isset($rdv))
            {{-- Motif --}}
            <div class="form-group">
                <label>Motif <span style="color:#aaa;font-size:12px">(optionnel)</span></label>
                <textarea name="motif"
                          class="form-control"
                          rows="3"
                          placeholder="Motif de la consultation...">{{ old('motif') }}</textarea>
            </div>
            @endunless

            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
                <a href="{{ route('secretaire.dashboard') }}" class="btn">Annuler</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($rdv) ? 'Confirmer la reprogrammation' : 'Enregistrer le RDV' }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
