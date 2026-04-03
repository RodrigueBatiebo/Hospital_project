@extends('medecin.layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="page-header">
    <h1>Bonjour, Dr. {{ auth()->user()->prenom }} {{ auth()->user()->nom }}</h1>
    <a href="{{ route('medecin.disponibilites.store') }}" class="btn btn-primary">
        + Ajouter disponibilité
    </a>
</div>

{{-- ── Stats ─────────────────────────────────────────────── --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="label">RDV aujourd'hui</div>
        <div class="value" style="color:#4a3a9a">{{ $stats['aujourd_hui'] ?? 0}}</div>
    </div>
    <div class="stat-card">
        <div class="label">Cette semaine</div>
        <div class="value">{{ $stats['semaine'] ?? 0}}</div>
    </div>
    <div class="stat-card">
        <div class="label">Disponibilités à venir</div>
        <div class="value" style="color:#0a3622">{{ $stats['disponibilites'] ?? 0}}</div>
    </div>
</div>

{{-- ── RDV du jour ─────────────────────────────────────────── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
    <p style="font-size:14px;font-weight:600">
        Rendez-vous du jour —
        {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}
    </p>
    <a href="{{ route('medecin.planning') }}" style="font-size:13px;color:#7F77DD;text-decoration:none">
        Voir le planning complet →
    </a>
</div>

@if($rdvDuJour->count() > 0)
<div class="table-wrapper" style="margin-bottom:24px">
    <table>
        <thead>
            <tr>
                <th>Heure</th>
                <th>Patient</th>
                <th>Motif</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rdvDuJour as $rdv)
            <tr>
                <td style="font-weight:600;color:#4a3a9a">
                    {{ \Carbon\Carbon::parse($rdv->heure)->format('H\hi') }}
                </td>
                <td>
                    {{ $rdv->patient->user->prenom ?? '' }}
                    {{ $rdv->patient->user->nom ?? '' }}
                </td>
                <td style="color:#666">{{ $rdv->motif ?? '—' }}</td>
                <td>
                    @if($rdv->statut == 'valide')
                        <span class="badge badge-ok">Validé</span>
                    @elseif($rdv->statut == 'en_attente')
                        <span class="badge badge-warn">En attente</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="card" style="text-align:center;padding:28px;margin-bottom:24px">
    <p style="color:#aaa;font-size:14px">Aucun rendez-vous prévu aujourd'hui.</p>
</div>
@endif

{{-- ── Prochaines disponibilités ──────────────────────────── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
    <p style="font-size:14px;font-weight:600">Prochaines disponibilités</p>
    <a href="{{ route('medecin.disponibilites') }}"
       style="font-size:13px;color:#7F77DD;text-decoration:none">
        Gérer mes disponibilités →
    </a>
</div>

@if($disponibilites->count() > 0)
<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure début</th>
                <th>Heure fin</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($disponibilites->take(5) as $dispo)
            <tr>
                <td>{{ \Carbon\Carbon::parse($dispo->date)->translatedFormat('d F Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($dispo->heure_debut)->format('H\hi') }}</td>
                <td>{{ \Carbon\Carbon::parse($dispo->heure_fin)->format('H\hi') }}</td>
                <td>
                    @if($dispo->statut == 'disponible')
                        <span class="badge badge-ok">Disponible</span>
                    @else
                        <span class="badge badge-gray">Occupé</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="card" style="text-align:center;padding:28px">
    <p style="color:#aaa;font-size:14px;margin-bottom:14px">Aucune disponibilité définie.</p>
    <a href="{{ route('medecin.disponibilites.store') }}" class="btn btn-primary">
        Ajouter une disponibilité
    </a>
</div>
@endif

@endsection
