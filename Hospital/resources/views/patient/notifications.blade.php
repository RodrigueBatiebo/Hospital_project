@extends('patient.layouts.app')

@section('title', 'Notifications')

@section('content')

<div class="page-header">
    <h1>Notifications</h1>
    @if($notifications->count() > 0)
    <form action="{{ route('patient.notifications.lire') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-sm">Tout marquer comme lu</button>
    </form>
    @endif
</div>

@forelse($notifications as $notif)
<div class="notif-card
    @if($notif->type == 'confirmation') notif-success
    @elseif($notif->type == 'reprogrammation') notif-info
    @elseif($notif->type == 'refus' || $notif->type == 'annulation') notif-warn
    @else notif-default
    @endif">

    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:6px">
        <p style="font-size:13px;font-weight:600">
            @if($notif->type == 'confirmation')
                Rendez-vous confirmé
            @elseif($notif->type == 'refus')
                Rendez-vous refusé
            @elseif($notif->type == 'annulation')
                Rendez-vous annulé
            @elseif($notif->type == 'reprogrammation')
                Rendez-vous reprogrammé
            @else
                Notification
            @endif
        </p>
        <p style="font-size:12px;color:#888">
            {{ \Carbon\Carbon::parse($notif->date_envoi)->diffForHumans() }}
        </p>
    </div>
    <p style="font-size:13px">{{ $notif->message }}</p>
</div>
@empty
<div class="card" style="text-align:center;padding:40px">
    <p style="color:#aaa;font-size:14px">Aucune notification pour le moment.</p>
</div>
@endforelse

@if($notifications->hasPages())
<div style="margin-top:16px">{{ $notifications->links() }}</div>
@endif

@endsection
