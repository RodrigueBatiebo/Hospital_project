@extends('patient.layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="page-header">
    <h1>Bonjour, {{ auth()->user()->prenom }} {{ auth()->user()->nom }}</h1>
    <a href="{{ route('patient.rendezvous.create') }}" class="btn btn-primary">
        + Prendre un RDV
    </a>
</div>

{{-- ── Stats ─────────────────────────────────────────────── --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="label">Prochains RDV</div>
        <div class="value" style="color:#0c3870">{{ $stats['valide'] ?? 0}}</div>
    </div>
    <div class="stat-card">
        <div class="label">En attente</div>
        <div class="value" style="color:#856404">{{ $stats['en_attente']}}</div>
    </div>
    <div class="stat-card">
        <div class="label">RDV passés</div>
        <div class="value">{{ $stats['passes'] ?? 0}}</div>
    </div>
</div>

{{-- ── Prochains rendez-vous ──────────────────────────────── --}}
<p style="font-size:14px;font-weight:600;margin-bottom:12px">Prochains rendez-vous</p>

@forelse($prochainRdv as $rdv)
<div class="rdv-card">
    <div class="rdv-card-header">
        <div>
            <p style="font-size:14px;font-weight:600">
                Dr. {{ $rdv->medecin->prenom }} {{ $rdv->medecin->nom }}
                — {{ $rdv->service->nom_service }}
            </p>
            <p style="font-size:13px;color:#666;margin-top:4px">
                {{ \Carbon\Carbon::parse($rdv->date)->translatedFormat('d F Y') }}
                à {{ \Carbon\Carbon::parse($rdv->heure)->format('H\hi') }}
            </p>
            @if($rdv->motif)
                <p style="font-size:12px;color:#999;margin-top:2px">Motif : {{ $rdv->motif }}</p>
            @endif
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:10px">
            @if($rdv->statut == 'valide')
                <span class="badge badge-ok">Validé</span>
            @elseif($rdv->statut == 'en_attente')
                <span class="badge badge-warn">En attente</span>
            @endif

            @if(in_array($rdv->statut, ['en_attente', 'valide']))
            <form action="{{ route('patient.rendezvous.annuler', $rdv->id) }}" method="POST"
                  onsubmit="return confirm('Confirmer l\'annulation de ce rendez-vous ?')">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Annuler</button>
            </form>
            @endif
        </div>
    </div>
</div>
@empty
<div class="card" style="text-align:center;padding:32px">
    <p style="color:#aaa;font-size:14px;margin-bottom:14px">Aucun rendez-vous à venir.</p>
    <a href="{{ route('patient.rendezvous.create') }}" class="btn btn-primary">Prendre un rendez-vous</a>
</div>
@endforelse

@if($prochainRdv->count() > 0)
<a href="{{ route('patient.rendezvous') }}" style="font-size:13px;color:#1D9E75;text-decoration:none">
    Voir tous mes rendez-vous →
</a>
@endif

@endsection
