@extends('secretaire.layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="page-header">
    <h1>Gestion des rendez-vous</h1>
    <a href="{{ route('secretaire.store') }}" class="btn btn-primary">
        + Ajouter manuellement
    </a>
</div>

{{-- ── Stats ─────────────────────────────────────────────── --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="label">En attente</div>
        <div class="value" style="color:#856404">{{ $stats['en_attente'] }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Validés aujourd'hui</div>
        <div class="value" style="color:#0a3622">{{ $stats['valide'] }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Refusés</div>
        <div class="value" style="color:#58151c">{{ $stats['refuse']}}</div>
    </div>
    <div class="stat-card">
        <div class="label">Total ce mois</div>
        <div class="value">{{ $stats['total']}}</div>
    </div>
</div>

{{-- ── Filtres ────────────────────────────────────────────── --}}
<div class="filter-bar">
    <a href="{{ route('secretaire.rendezvous') }}"
       class="btn {{ !request('statut') ? 'btn-primary' : '' }}">Tous</a>
    <a href="{{ route('secretaire.rendezvous', ['statut' => 'en_attente']) }}"
       class="btn {{ request('statut') == 'en_attente' ? 'btn-primary' : '' }}">En attente</a>
    <a href="{{ route('secretaire.rendezvous', ['statut' => 'valide']) }}"
       class="btn {{ request('statut') == 'valide' ? 'btn-primary' : '' }}">Validés</a>
    <a href="{{ route('secretaire.rendezvous', ['statut' => 'refuse']) }}"
       class="btn {{ request('statut') == 'refuse' ? 'btn-primary' : '' }}">Refusés</a>
</div>

{{-- ── Tableau ─────────────────────────────────────────────── --}}
<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Patient</th>
                <th>Médecin</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Motif</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rendezvous as $rdv)
            <tr>
                <td>{{ $rdv->patient->user->prenom }} {{ $rdv->patient->user->nom }}</td>
                <td style="color:#666">Dr. {{ $rdv->medecin->user->nom }}</td>
                <td>{{ \Carbon\Carbon::parse($rdv->date)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($rdv->heure)->format('H\hi') }}</td>
                <td style="color:#666">{{ $rdv->motif ?? '—' }}</td>
                <td>
                    @if($rdv->statut == 'en_attente')
                        <span class="badge badge-warn">En attente</span>
                    @elseif($rdv->statut == 'valide')
                        <span class="badge badge-ok">Validé</span>
                    @elseif($rdv->statut == 'refuse')
                        <span class="badge badge-danger">Refusé</span>
                    @else
                        <span class="badge" style="background:#f0f0f0;color:#666">Annulé</span>
                    @endif
                </td>
                <td>
                    @if($rdv->statut == 'en_attente')
                    <div style="display:flex;gap:6px">
                        <form action="{{ route('secretaire.valider', $rdv->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Valider</button>
                        </form>
                        <button class="btn btn-danger btn-sm"
                            onclick="document.getElementById('modal-refus-{{ $rdv->id }}').style.display='flex'">
                            Refuser
                        </button>
                        <a href="{{ route('secretaire.reprogrammer', $rdv->id) }}"
                           class="btn btn-sm">Reprogrammer</a>
                    </div>
                    @else
                        <span style="color:#aaa;font-size:12px">—</span>
                    @endif
                </td>
            </tr>

            {{-- Modal refus --}}
            @if($rdv->statut == 'en_attente')
            <div id="modal-refus-{{ $rdv->id }}"
                 style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:100;align-items:center;justify-content:center">
                <div class="card" style="width:400px">
                    <h3 style="font-size:15px;margin-bottom:14px">Refuser le rendez-vous</h3>
                    <form action="{{ route('secretaire.refuser', $rdv->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Motif du refus (optionnel)</label>
                            <textarea name="motif_refus" class="form-control" rows="3"
                                placeholder="Expliquez la raison du refus..."></textarea>
                        </div>
                        <div style="display:flex;gap:8px;justify-content:flex-end">
                            <button type="button" class="btn"
                                onclick="document.getElementById('modal-refus-{{ $rdv->id }}').style.display='none'">
                                Annuler
                            </button>
                            <button type="submit" class="btn btn-danger">Confirmer le refus</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:30px;color:#aaa">
                    Aucun rendez-vous trouvé.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($rendezvous->hasPages())
<div style="margin-top:16px">{{ $rendezvous->appends(request()->query())->links() }}</div>
@endif

@endsection
