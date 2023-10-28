@extends('layouts.app')

{{-- # METTIAMO LA CDN DI FONTAWESOME PER LE ICONE --}}
@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')
  <div class="container">
    {{-- # CI CREIAMO UN PULSANTE CHE CI PORTA AL FORM PER CREARE UN NUOVO PROJECT --}}
    <a href="{{ route('admin.projects.create') }}" class="btn btn-success my-4">Crea progetto</a>

    {{-- # CI CREIAMO UNA TABELLA CHE CONTIENE LA LISTA DEI VARI ELEMENTI DEL DB --}}
    <table class="table">
      <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Nome</th>
          {{-- ! AGGIUNGIAMO UN COLONNA TIPO PER IL TYPE --}}
          <th scope="col">Tipo</th>
          <th scope="col">Descrizione</th>
          <th scope="col">Link</th>
          <th scope="col"></th>
        </tr>
      </thead>
      <tbody>
        {{-- # FACCIO UN CICLO E STAMPO UNA RIGA PER OGNI ELEMENTO DEL DB --}}
        @forelse($projects as $project)
          <tr>
            <th scope="row">{{ $project->id }}</th>
            <td>{{ $project->name }}</td>
            {{-- ! USIAMO IL METODO CHE ABBIAMO FATTO ANCHE PER LA SHOW --}}
            <td>{!! $project->getTypeBadge() !!}</td>
            <td>{{ $project->description }}</td>
            <td>{{ $project->link }}</td>

            <td>
              <div class="d-flex align-items-center">
                {{-- # CI CREIAMO UN PULSANTE CHE CI PORTA AL DETTAGLIO DEL PROJECT --}}
                <a href="{{ route('admin.projects.show', $project) }}" class="d-inline-block mx-1"><i
                    class="fa-solid fa-eye"></i></a>
                {{-- # CI CREIAMO UN PULSANTE CHE CI PORTA ALLA MODIFICA DEL PROJECT --}}
                <a href="{{ route('admin.projects.edit', $project) }}" class="d-inline-block mx-1"><i
                    class="fa-solid fa-pencil"></i></a>
                {{-- # CI CREIAMO UN PULSANTE CHE ELIMINA IL PROJECT --}}
                {{-- # IMPORTANTE METTERE # NELL'href --}}
                <a href="#" class="d-inline-block mx-1 text-danger" data-bs-toggle="modal"
                  data-bs-target="#delete-modal-{{ $project->id }}"><i class="fa-solid fa-trash"></i></a>
              </div>

              {{-- ! MODAL PRESA DA BOOTSTRAP --}}
              <div class="modal fade" id="delete-modal-{{ $project->id }}" tabindex="-1"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h1 class="modal-title fs-5" id="exampleModalLabel">Elimina progetto</h1>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      Sei sicuro di voler eliminare definitivamente il progetto: <h6>{{ $project->name }}?</h6>
                    </div>
                    <div class="modal-footer">
                      {{-- ! QUI ABBIAMO I DUE TASTI DELLA MODAL --}}
                      {{-- ! IL PRIMO RIMANE COSI' COM'E' PER CHIUDERE SEMPLICEMENTE LA MODAL --}}
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                      {{-- ! NEL SECONDO METTIAMO IL FORM BASE DI CANCELLAZIONE AL POSTO DEL SINGOLO TASTO --}}
                      <form action="{{ route('admin.projects.destroy', $project) }}" method="POST">
                        @method('DELETE')
                        @csrf
                        <button class="btn btn-primary">Elimina</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5">Non ci sono progetti</td>
          </tr>
        @endforelse
      </tbody>
    </table>
    {{ $projects->links('pagination::bootstrap-5') }}
  </div>
@endsection
