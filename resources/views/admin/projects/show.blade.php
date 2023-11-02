{{-- # CREO UN TEMPLATE DEL DETTAGLIO ESTENDENDO IL layouts.app --}}
@extends('layouts.app')

@section('content')
  <div class="container mt-5">
    {{-- # PULSANTE CHE CI RIPORTA ALLA LISTA QUINDI ALL'index --}}
    <a href="{{ route('admin.projects.index') }}" class="btn btn-success">Torna alla lista</a>
    <hr>
    <div class="row g-5 mt-3">
      <div class="col-3">
        <p>
          <strong>Project Name:</strong><br>
          {{ $project->name }}
        </p>
      </div>


      <div class="col-3">
        <p>
          <strong>Type:</strong><br>
          {{-- # QUI STAMPIAMO LA LABEL DEL TYPE CHE ABBIAMO COLLEGATO TRAMITE RELAZIONE --}}
          {{-- # USIAMO IL '?' CHE E' IL NULL OPERATOR IN MANIERA TALE CHE SE CAPITA SENZA TYPE NON DARA' --}}
          {{-- # ERRORE MA DARA' IL CAMPO VUOTO, POI PER FARE UNA COSA MIGLIORE POSSIAMO FARE UNA FUNZIONE --}}
          {{-- {{ $project->type?->label }} --}}

          {{-- # GETTER CHE METTE TIPO UNTYPE O QUELLO CHE VOGLIAMO NOI --}}
          {{-- ! I DOPPI PUNTI !! SONO PER STAMPARE L'HTML DEL TERNARIO ALTRIMENTI STAMPEREBBE L'HTML LETTERALMENTE --}}
          {!! $project->getTypeBadge() !!}
        </p>
      </div>

      <div class="col-3">
        <p>
          {{-- # CI STAMPIAMO I BADGES DELLE TECNOLOGIE CON IL GETTER CHE ABBIAMO FATTO --}}
          <strong>Tecnologies:</strong><br>
          {!! $project->getTecnologyBadges() !!}
        </p>
      </div>

      <div class="col-3">
        <p>
          <strong>Link:</strong><br>
          <a href="#">{{ $project->link }}</a>
        </p>
      </div>

      <div class="col-12">
        <p>
          <strong>Description</strong><br>
          {{ $project->description }}
        </p>
      </div>
    </div>
  </div>
@endsection
