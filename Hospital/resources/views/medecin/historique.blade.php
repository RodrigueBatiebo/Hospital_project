@extends('medecin.layouts.app')

@section('title', 'Historique')

@section('content')

<div class="page-header">
    <h1>Historique des consultations</h1>

    {{-- Recherche --}}
    <form method="GET" action="{{ route('medecin.historique') }}"
          style="display:flex;gap:8px">
        <input type="text"
               name="search"
               class="form-control"
               style="width:220px"
               placeholder="Rechercher un patient..."
               value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary btn-sm">Rechercher</button>
        @if(request('search'))
        <a href="{{ route('medecin.historique') }}" class="btn btn-sm">Effacer</a>
        @endif
    </form>
</div>

{{-- ── Stats historique ────────────────────────────────────── --}}
<div class="stats-grid" style="margin-bottom:20px">
    <div class="stat-card">
        <div class="label">Total consultations</div>
        <div class="value" style="color:#4a3a9a">{{ $stats['total'] }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Ce mois</div>
        <div class="value">{{ $stats['ce_mois'] }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Annulés</div>
        <div class="value" style="color:#58151c">{{ $stats['annules'] }}</div>
    </div>
</div>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Patient</th>
                <th>Service</th>
                <th>Motif</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($historique as $rdv)
            <tr>
                <td>{{ \Carbon\Carbon::parse($rdv->date)->translatedFormat('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($rdv->heure)->format('H\hi') }}</td>
                <td style="font-weight:500">
                    {{ $rdv->patient->user->prenom ?? '' }}
                    {{ $rdv->patient->user->nom ?? '' }}
                </td>
                <td style="color:#666">{{ $rdv->service->nom_service }}</td>
                <td style="color:#666">{{ $rdv->motif ?? '—' }}</td>
                <td>
                    @if($rdv->statut == 'valide')
                        <span class="badge badge-ok">Effectué</span>
                    @elseif($rdv->statut == 'refuse')
                        <span class="badge badge-danger">Refusé</span>
                    @elseif($rdv->statut == 'annule')
                        <span class="badge badge-gray">Annulé</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <p>
                            @if(request('search'))
                                Aucun résultat pour "{{ request('search') }}".
                            @else
                                Aucune consultation passée.
                            @endif
                        </p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($historique->hasPages())
<div style="margin-top:16px">{{ $historique->appends(request()->query())->links() }}</div>
@endif

@endsection
