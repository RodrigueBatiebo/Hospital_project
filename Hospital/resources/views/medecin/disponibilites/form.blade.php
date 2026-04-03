@extends('medecin.layouts.app')

@section('title', isset($disponibilite) ? 'Modifier disponibilité' : 'Ajouter disponibilité')

@section('content')

<div class="page-header">
    <h1>{{ isset($disponibilite) ? 'Modifier la disponibilité' : 'Ajouter une disponibilité' }}</h1>
</div>

<div style="max-width:500px">
    <div class="card">
        <form action="{{ isset($disponibilite)
                ? route('medecin.disponibilites.update', $disponibilite->id)
                : route('medecin.disponibilites.store') }}"
              method="POST">
            @csrf
            @if(isset($disponibilite))
                @method('PUT')
            @endif

            {{-- Date --}}
            <div class="form-group">
                <label>Date</label>
                <input type="date"
                       name="date"
                       class="form-control"
                       value="{{ old('date', isset($disponibilite) ? $disponibilite->date : '') }}"
                       min="{{ date('Y-m-d') }}"
                       required>
                @error('date')
                    <span style="color:#d9534f;font-size:12px">{{ $message }}</span>
                @enderror
            </div>

            {{-- Heure début et fin --}}
            <div class="form-row cols-2">
                <div class="form-group">
                    <label>Heure de début</label>
                    <input type="time"
                           name="heure_debut"
                           class="form-control"
                           value="{{ old('heure_debut', isset($disponibilite)
                                ? \Carbon\Carbon::parse($disponibilite->heure_debut)->format('H:i')
                                : '') }}"
                           required>
                    @error('heure_debut')
                        <span style="color:#d9534f;font-size:12px">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Heure de fin</label>
                    <input type="time"
                           name="heure_fin"
                           class="form-control"
                           value="{{ old('heure_fin', isset($disponibilite)
                                ? \Carbon\Carbon::parse($disponibilite->heure_fin)->format('H:i')
                                : '') }}"
                           required>
                    @error('heure_fin')
                        <span style="color:#d9534f;font-size:12px">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Info durée --}}
            <div id="duree-info"
                 style="display:none;background:#f5f0ff;border:1px solid #7F77DD;border-radius:8px;
                        padding:10px 14px;font-size:13px;color:#4a3a9a;margin-bottom:16px">
                Durée : <span id="duree-text" style="font-weight:600"></span>
            </div>

            {{-- Boutons --}}
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
                <a href="{{ route('medecin.disponibilites') }}" class="btn">Annuler</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($disponibilite) ? 'Mettre à jour' : 'Enregistrer' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function calculerDuree() {
    const debut = document.querySelector('[name="heure_debut"]').value;
    const fin   = document.querySelector('[name="heure_fin"]').value;
    const info  = document.getElementById('duree-info');
    const text  = document.getElementById('duree-text');

    if (debut && fin) {
        const [hd, md] = debut.split(':').map(Number);
        const [hf, mf] = fin.split(':').map(Number);
        const totalMin = (hf * 60 + mf) - (hd * 60 + md);

        if (totalMin > 0) {
            const h = Math.floor(totalMin / 60);
            const m = totalMin % 60;
            text.textContent = h > 0 ? `${h}h${m > 0 ? m + 'min' : ''}` : `${m} minutes`;
            info.style.display = 'block';
        } else {
            info.style.display = 'none';
        }
    }
}

document.querySelector('[name="heure_debut"]').addEventListener('change', calculerDuree);
document.querySelector('[name="heure_fin"]').addEventListener('change', calculerDuree);
</script>

@endsection
