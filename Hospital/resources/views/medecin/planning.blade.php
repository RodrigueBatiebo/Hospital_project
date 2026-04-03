@extends('medecin.layouts.app')

@section('title', 'Mon planning')

@section('content')

<div class="page-header">
    <h1>Mon planning</h1>
    {{-- Navigation semaine --}}
    <div style="display:flex;align-items:center;gap:12px">
        <a href="{{ route('medecin.planning', ['semaine' => $semainePrecedente]) }}"
           class="btn btn-sm">← Semaine précédente</a>
        <span style="font-size:13px;font-weight:500;color:#555">
            Semaine du {{ $debutSemaine->translatedFormat('d F') }}
            au {{ $finSemaine->translatedFormat('d F Y') }}
        </span>
        <a href="{{ route('medecin.planning', ['semaine' => $semaineSuivante]) }}"
           class="btn btn-sm">Semaine suivante →</a>
    </div>
</div>

<div class="week-grid">
    @foreach($joursOuvres as $jour)
    <div class="day-col {{ $jour->isToday() ? 'today' : '' }}">
        <p class="day-title">
            {{ $jour->translatedFormat('l') }}<br>
            <span style="font-weight:400;color:#aaa">{{ $jour->format('d/m') }}</span>
        </p>

        @php
            $rdvDuJour = $rendezVous->filter(function($rdv) use ($jour) {
                return \Carbon\Carbon::parse($rdv->date)->isSameDay($jour);
            })->sortBy('heure');
        @endphp

        @forelse($rdvDuJour as $rdv)
        <div class="slot
            @if($rdv->statut == 'valide') slot-booked
            @elseif($rdv->statut == 'en_attente') slot-waiting
            @elseif($rdv->statut == 'annule') slot-canceled
            @else slot-free @endif">
            <span>{{ \Carbon\Carbon::parse($rdv->heure)->format('H\hi') }}</span>
            <span style="font-size:11px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:80px">
                @if(in_array($rdv->statut, ['valide', 'en_attente']))
                    {{ $rdv->patient->user->prenom ?? '' }}
                    {{ substr($rdv->patient->user->nom ?? '', 0, 1) }}.
                @elseif($rdv->statut == 'annule')
                    Annulé
                @endif
            </span>
        </div>
        @empty
        <div class="slot slot-free">
            <span style="font-size:11px">Aucun RDV</span>
        </div>
        @endforelse
    </div>
    @endforeach
</div>

{{-- Légende --}}
<div style="display:flex;gap:16px;margin-top:16px;font-size:12px;color:#666">
    <div style="display:flex;align-items:center;gap:6px">
        <span style="width:12px;height:12px;border-radius:3px;background:#d1e7dd;display:inline-block"></span>
        Validé
    </div>
    <div style="display:flex;align-items:center;gap:6px">
        <span style="width:12px;height:12px;border-radius:3px;background:#fff3cd;display:inline-block"></span>
        En attente
    </div>
    <div style="display:flex;align-items:center;gap:6px">
        <span style="width:12px;height:12px;border-radius:3px;background:#f8d7da;display:inline-block"></span>
        Annulé
    </div>
    <div style="display:flex;align-items:center;gap:6px">
        <span style="width:12px;height:12px;border-radius:3px;background:#f5f5f5;display:inline-block"></span>
        Libre
    </div>
</div>

@endsection
