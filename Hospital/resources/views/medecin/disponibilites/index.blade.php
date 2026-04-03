@extends('medecin.layouts.app')

@section('title', 'Mes disponibilités')

@section('content')

<div class="page-header">
    <h1>Mes disponibilités</h1>
    <a href="{{ route('medecin.disponibilites.create') }}" class="btn btn-primary">
        + Ajouter une disponibilité
    </a>
</div>

{{-- ── Filtres ────────────────────────────────────────────── --}}
<div style="display:flex;gap:8px;margin-bottom:16px">
    <a href="{{ route('medecin.disponibilites') }}"
       class="btn {{ !request('statut') ? 'btn-primary' : '' }}">Toutes</a>
    <a href="{{ route('medecin.disponibilites', ['statut' => 'disponible']) }}"
       class="btn {{ request('statut') == 'disponible' ? 'btn-primary' : '' }}">Disponibles</a>
    <a href="{{ route('medecin.disponibilites', ['statut' => 'occupe']) }}"
       class="btn {{ request('statut') == 'occupe' ? 'btn-primary' : '' }}">Occupées</a>
</div>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure début</th>
                <th>Heure fin</th>
                <th>Durée</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($disponibilites as $dispo)
            <tr>
                <td style="font-weight:500">
                    {{ \Carbon\Carbon::parse($dispo->date)->translatedFormat('l d F Y') }}
                </td>
                <td>{{ \Carbon\Carbon::parse($dispo->heure_debut)->format('H\hi') }}</td>
                <td>{{ \Carbon\Carbon::parse($dispo->heure_fin)->format('H\hi') }}</td>
                <td style="color:#666">
                    @php
                        $debut = \Carbon\Carbon::parse($dispo->heure_debut);
                        $fin   = \Carbon\Carbon::parse($dispo->heure_fin);
                        $duree = $debut->diffInMinutes($fin);
                    @endphp
                    {{ $duree }} min
                </td>
                <td>
                    @if($dispo->statut == 'disponible')
                        <span class="badge badge-ok">Disponible</span>
                    @else
                        <span class="badge badge-gray">Occupé</span>
                    @endif
                </td>
                <td>
                    @if($dispo->statut == 'disponible')
                    <div style="display:flex;gap:6px">
                        <a href="{{ route('medecin.disponibilites.edit', $dispo->id) }}"
                           class="btn btn-sm">Modifier</a>
                        <form action="{{ route('medecin.disponibilites.destroy', $dispo->id) }}"
                              method="POST"
                              onsubmit="return confirm('Supprimer cette disponibilité ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    </div>
                    @else
                        <span style="color:#aaa;font-size:12px">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <p>Aucune disponibilité trouvée.</p>
                        <a href="{{ route('medecin.disponibilites.create') }}" class="btn btn-primary">
                            Ajouter une disponibilité
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($disponibilites->hasPages())
<div style="margin-top:16px">{{ $disponibilites->appends(request()->query())->links() }}</div>
@endif

@endsection
