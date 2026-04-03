@extends('patient.layouts.app')

@section('title', 'Mes rendez-vous')

@section('content')

<div class="page-header">
    <h1>Mes rendez-vous</h1>
    <a href="{{ route('patient.rendezvous.create') }}" class="btn btn-primary">+ Nouveau RDV</a>
</div>

{{-- ── Filtres ────────────────────────────────────────────── --}}
<div class="filter-bar" style="display:flex;gap:8px;margin-bottom:16px">
    <a href="{{ route('patient.rendezvous') }}"
       class="btn {{ !request('statut') ? 'btn-primary' : '' }}">Tous</a>
    <a href="{{ route('patient.rendezvous', ['statut' => 'en_attente']) }}"
       class="btn {{ request('statut') == 'en_attente' ? 'btn-primary' : '' }}">En attente</a>
    <a href="{{ route('patient.rendezvous', ['statut' => 'valide']) }}"
       class="btn {{ request('statut') == 'valide' ? 'btn-primary' : '' }}">Validés</a>
    <a href="{{ route('patient.rendezvous', ['statut' => 'annule']) }}"
       class="btn {{ request('statut') == 'annule' ? 'btn-primary' : '' }}">Annulés</a>
</div>

{{-- ── Tableau ─────────────────────────────────────────────── --}}
<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Médecin</th>
                <th>Service</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Motif</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rendezVous as $rdv)
            <tr>
                <td style="font-weight:500">Dr. {{ $rdv->medecin->prenom }} {{ $rdv->medecin->nom }}</td>
                <td style="color:#666">{{ $rdv->service->nom_service }}</td>
                <td>{{ \Carbon\Carbon::parse($rdv->date)->translatedFormat('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($rdv->heure)->format('H\hi') }}</td>
                <td style="color:#666">{{ $rdv->motif ?? '—' }}</td>
                <td>
                    @if($rdv->statut == 'en_attente')
                        <span class="badge badge-warn">En attente</span>
                    @elseif($rdv->statut == 'valide')
                        <span class="badge badge-ok">Validé</span>
                    @elseif($rdv->statut == 'refuse')
                        <span class="badge badge-danger">Refusé</span>
                    @elseif($rdv->statut == 'annule')
                        <span class="badge badge-gray">Annulé</span>
                    @else
                        <span class="badge badge-gray">Passé</span>
                    @endif
                </td>
                <td>
                    @if(in_array($rdv->statut, ['en_attente', 'valide']) && \Carbon\Carbon::parse($rdv->date)->isFuture())
                    <form action="{{ route('patient.rendezvous.annuler', $rdv->id) }}" method="POST"
                          onsubmit="return confirm('Confirmer l\'annulation ?')">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Annuler</button>
                    </form>
                    @else
                        <span style="color:#aaa;font-size:12px">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <p>Aucun rendez-vous trouvé.</p>
                        <a href="{{ route('patient.rendezvous.create') }}" class="btn btn-primary">
                            Prendre un rendez-vous
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($rendezVous->hasPages())
<div style="margin-top:16px">{{ $rendezVous->appends(request()->query())->links() }}</div>
@endif

@endsection
