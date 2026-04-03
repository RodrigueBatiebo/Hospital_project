@extends('secretaire.layouts.app')

@section('title', 'Planning')

@section('content')

<div class="page-header">
    <h1>Planning des médecins — Service {{ auth()->user()->secretaire->service->nom_service ?? '' }}</h1>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px">
    @forelse($medecins as $medecin)
    <div class="card">
        {{-- En-tête médecin --}}
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
            <div class="avatar" style="background:#d0e4f7;color:#0c3870;width:40px;height:40px;font-size:13px">
                {{ strtoupper(substr($medecin->prenom, 0, 1) . substr($medecin->nom, 0, 1)) }}
            </div>
            <div>
                <p style="font-size:14px;font-weight:600">Dr. {{ $medecin->prenom }} {{ $medecin->nom }}</p>
                <p style="font-size:12px;color:#888">{{ $medecin->specialite }}</p>
            </div>
            <span class="badge badge-ok" style="margin-left:auto">Disponible</span>
        </div>

        {{-- Créneaux du jour --}}
        <div class="planning-grid">
            @forelse($medecin->rendezVous->where('date', today()->toDateString()) as $rdv)
                <div class="planning-slot
                    @if($rdv->statut == 'valide') slot-booked
                    @elseif($rdv->statut == 'en_attente') slot-waiting
                    @else slot-free @endif">
                    <span>{{ \Carbon\Carbon::parse($rdv->heure)->format('H\hi') }}</span>
                    <span style="font-size:12px">
                        @if($rdv->statut == 'valide')
                            {{ $rdv->patient->prenom }} {{ substr($rdv->patient->nom, 0, 1) }}.
                        @elseif($rdv->statut == 'en_attente')
                            En attente
                        @endif
                    </span>
                </div>
            @empty
                <div class="planning-slot slot-free">
                    <span style="font-size:13px;color:#aaa">Aucun RDV aujourd'hui</span>
                </div>
            @endforelse
        </div>
    </div>
    @empty
    <p style="color:#aaa">Aucun médecin dans ce service.</p>
    @endforelse
</div>

@endsection
