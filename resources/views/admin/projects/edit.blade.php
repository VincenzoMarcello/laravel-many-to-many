@extends('layouts.app')

@section('content')
  <div class="container mt-4">
    {{-- # PULSANTE CHE CI RIPORTA ALLA LISTA QUINDI ALL'index --}}
    <a href="{{ route('admin.projects.index') }}" class="btn btn-success">Torna alla lista</a>
    <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-warning">Vai al dettaglio</a>
    <hr>
    <h2>Modifica progetto</h2>
    <form action="{{ route('admin.projects.update', $project) }}" enctype="multipart/form-data" method="POST">
      @csrf
      @method('PATCH')
      <div class="row g-3">

        {{-- ! QUI CI COPIAMO LA RIGA DAL CREATE E AGGIUNGIAMO LA VISUALIZZAZIONE DELL'IMMAGINE ALLA FINE --}}
        <div class="col-12">
          <div class="row">
            <div class="col-8">
              <label for="cover_image" class="form-label">Scegli immagine</label>
              <input class="form-control @error('cover_image') is-invalid @enderror" type="file" id="cover_image"
                name="cover_image" value="{{ old('cover_image') }}">
              @error('cover_image')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-4">
              <img src="{{ asset('/storage/' . $project->cover_image) }}" class="img-fluid" alt="">
            </div>
          </div>
        </div>

        <div class="col-12">
          <label for="name" class="form-label">Name</label>
          {{-- ! QUI METTIAMO NELL'INPUT IL VECCHIO VALORE E IL GLI ERROR PER LA VALIDAZIONE --}}
          <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name"
            value="{{ old('name') ?? $project->name }}">
          {{-- ! QUI ABBIAMO IL MESSAGGIO DI ERRORE --}}
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12">
          <label for="type_id" class="form-label">Tipo</label>
          <select name="type_id" id="type_id" class="form-select @error('type_id') is-invalid @enderror">Seleziona un
            Tipo
            <option value="">Nessun Tipo</option>
            <option value="100" @if (old('type_id') == '100') selected @endif>Non Valido</option>
            @foreach ($types as $type)
              <option value="{{ $type->id }}" @if (old('type_id') && $type->type && old('type_id') == $type->type->id) selected @endif>{{ $type->label }}
              </option>
            @endforeach
          </select>

          @error('type_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- # CREIAMOCI UNA CHECK-BOX CON I VALORI DELLE TECNOLOGIE --}}
        <div class="col-12">
          <div class="form-check @error('technologies') is-invalid @enderror">
            @foreach ($technologies as $technology)
              <div class="col-2">
                {{-- ! NEL NAME SI METTONO LE [] PERCHè ALTRIMENTI ANCHE SELEZIONIAMO PIU' CHECKBOX NE ARRIVERà SOLO UNA --}}
                {{-- ! INVECE METTENDO LE [] ARRIVA UN ARRAY CHE CONTIENE TUTTE LE CHECK SEGNATE --}}
                <input type="checkbox" name="technologies[]" id="technology-{{ $technology->id }}"
                  value="{{ $technology->id }}" class="form-check-input"
                  @if (in_array($technology->id, old('technologies') ?? $technology_ids)) checked @endif>
                <label for="technology-{{ $technology->id }}">{{ $technology->label }}</label>
              </div>
            @endforeach
          </div>
        </div>

        <div class="col-12">
          <label for="link" class="form-label">Link</label>
          <input class="form-control @error('link') is-invalid @enderror" type="url" id="link" name="link"
            value="{{ old('link') ?? $project->link }}">

          @error('link')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- # NELLA TEXTAREA IL VALUE VA MESSO DIRETTAMENTE NEL TAG E NON COME ATTRIBUTO --}}
        <div class="col-12">
          <label for="description" class="form-label">Description</label>
          <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('number') ?? $project->description }}
          </textarea>

          @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      {{-- ! RICORDA CHE IL BUTTON DELL'INVIO DEL FORM NON DEVE ESSERE MAI TYPE BUTTON --}}
      <button class="btn btn-success mt-3">Salva progetto</button>
    </form>
  @endsection
