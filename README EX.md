# TRACCIA

Creiamo con Laravel il nostro sistema di gestione del nostro Portfolio di progetti.
Oggi iniziamo un nuovo progetto che si arricchirà nel corso delle prossime lezioni: man mano aggiungeremo funzionalità e vedremo la nostra applicazione crescere ed evolvere.

Nel pomeriggio, rifate ciò che abbiamo visto insieme stamattina stilando tutto a vostro piacere utilizzando SASS.

**Descrizione:**
Ripercorriamo gli steps fatti a lezione ed iniziamo un nuovo progetto partendo dalla repo template https://github.com/TizianoN/103-laravel-boilerplate-auth
Iniziamo con il definire il layout, modello, migrazione, controller e rotte necessarie per il sistema portfolio:

-   Autenticazione: si parte con l'autenticazione e la creazione di un layout per back-office
-   Creazione del modello Project con relativa migrazione, seeder, controller e rotte
-   Per la parte di back-office creiamo un resource controller Admin\ProjectController per gestire tutte le operazioni CRUD dei progetti

## Bonus

Implementiamo la validazione dei dati dei Progetti nelle operazioni CRUD.

# SVOLGIMENTO

-   installo le dipendenze di back-end e di front-end e genero la key:

```
 composer i | npm i | php artisan key:generate
```

-   duplico il file env e faccio il collegamento al database del server ma prima:

    -   creo un database su phpMyAdmin in questo caso lo chiamo come la repo 'laravel-auth'
        e la tabella la chiamo 'projects'

-   faccio il comando sotto per fare il migrate che genererà la tabella degli utenti per il login:

```
php artisan migrate
```

-   e faccio partire i server

```
php artisan serve | npm run dev
```

-   Ora ci creiamo il model il resource controller e il seeder:

```
php artisan make:model Project -mscr
```

-   Ora andiamo nelle migration e ci creaimo le colonne della tabella projects del database e quindi andiamo su 'create_projects_table' e scegliamo cosa mettere:

```php
   public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); <--
            $table->text('description'); <--
            $table->string('link'); <--
            $table->string('slug'); <--
            $table->timestamps();
        });
    }
```

-   e facciamo il migrate:

```
 php artisan migrate
```

-   Ora andiamo nel seeder precisamente ProjectSeeder:

```php
  public function run(Faker $faker)
    {
        for ($i = 0; $i < 50; $i++) {
            $project = new Project();
            $project->name = $faker->catchPhrase();
            $project->description = $faker->text();
            $project->link = $faker->url();
            $project->slug = Str::slug($project->name);
            $project->save();
        }
    }
```

-   e ci importiamo le classi che ci servono:

```php
use App\Models\Project;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
```

-   dopodichè lanciamo il comando per salvare i valori nel db:

```
php artisan db:seed --class=ProjectSeeder
```

**ATTENZIONE:** siccome potrebbe capitare di dover fare un reset con il comando

```
 php artisan migrate:reset
```

per ad esempio inserire un nuovo dato nel migrate, facendo il reset perderei anche i dati degli users quindi quelli del login e si dovrebbero registrare nuovamente quindi per ovviare a questo facciamo un seeder solo degli users quindi creiamocelo:

```
 php artisan make:seeder UserSeeder
```

-   andiamo in UserSeeder:

```php
 public function run()
    {
        $user = new User();
        $user->name = "Admin";
        $user->email = "admin@admin.it";
        // # QUI USIAMO UN METODO PER HASHARE LA PASSWORD
        $user->password = Hash::make("password");
        $user->save();
    }

```

-   e ci importiamo questo per l'Hash:

```php
use Illuminate\Support\Facades\Hash;
```

-   Ora se facciamo il reset comunque verranno cancellate tutte le tabelle ma con un seed ricarichiamo i dati dell'utente il problema è che dobbiamo fare il migrate e due seed:

```
 php artisan migrate
```

```
php artisan db:seed --class=ProjectSeeder
```

```
php artisan db:seed --class=UserSeeder
```

-   Per ovviare anche a questo possiamo usare il DatabaseSeeder.php in seeders:

```php
 public function run()
    {
        // # CHIAMIAMO UN METODO call() CHE CONTERRA' UN ARRAY CON TUTTI I SEEDER
        // # CHE VOGLIAMO CHIAMARE IN MANIERA TALE CHE SE FACCIAMO UN REFRESH O UN RESET
        // # BASTERA' FARE php artisan db:seed E TUTTI I SEEDER NELLA call() SI AVVIERANNO
        $this->call([
            ProjectSeeder::class,
            UserSeeder::class
        ]);
    }
```

-   basterà poi fare:

```
 php artisan db:seed

 ATTENZIONE: se non ci sono classi da seedare nella call() questo comando non farà nulla
```

-   possiamo anche fare tutto in un colpo solo:

```
 php artisan migrate:refresh --seed
```

-   passiamo ai controller prima cosa da fare è spostare il ProjectController in Admin
    visto che è una cosa che riguarda l'utente loggato oppure cancellarlo e ricrearlo:

    1.  Se lo spostiamo cosa dovremo fare:

    ```
    Aggiungiamo \Admin alla fine

    namespace App\Http\Controllers\Admin;
    ```

    e importiamo il Controller

    ```php
    use App\Http\Controllers\Controller;
    ```

    2.  Se lo cancelliamo:

    ```
    lanciamo semplicemente il comando:
    php artisan make:controller Admin\ProjectController -r
    ```

-   Ora nel resource controller (ProjectController) ci importiamo il Model Project nel nostro caso già lo abbiamo:

```php
use App\Models\Project;
```

## CREAZIONE INDEX

-   poi iniziamo con le CRUD e l'index, per prima cosa ci andiamo a creare
    nelle views in admin la cartellina projects e qui dentro metteremo il file
    index.blade.php

-   dopodiché dobbiamo aggiungere le rotte per il resource controller nel file web.php:

```php
Route::resource('projects', ProjectController::class);
```

-   e aggiungiamo se manca:

```php
use App\Http\Controllers\Admin\ProjectController;
```

-   poi in ProjectController:

```php
   public function index()
    {
        $projects = Project::all();
        return view('admin.projects.index', compact('projects'));
    }
```

-   e aggiungiamo questo se manca:

```php
use App\Models\Project;
```

-   per ora in index.blade.php vediamo i risultati in un dump:

```php
@extends('layouts.app')

@section('content')
  <div class="container">
    @dump($projects)
  </div>
@endsection
```

-   ma siccome sono tanti possiamo decidere di fare la paginazione in ProjectController:

```php
$projects = Project::paginate(15);
```

-   e in index.blade.php:

```php
{{ $projects->links('pagination::bootstrap-5') }}
```

-   ora mettiamo i dati in un tabella invece che un dump

-   ora siccome possiamo accedere all'index solo attraverso l'aggiunta della query
    presa dalla route:list direttamente nell'URL ci conviene creare un link che raggiunge l'index dalla navbar
    **IMPORTANTE:** questo link per raggiungere l'index lo potrà vedere solo l'utente loggato!

    ```php
      <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.projects.index') }}">Projects</a>
      </li>
    ```

    quindi aggiungiamo questo alla navbar nel blocco degli @else con la rotta per l'index
    ora vedremo il link Projects nella navbar solo se siamo loggati però

    ```php
    {{__('Register')}}
    questo doppio '__' si usa per i siti multiligua però noi
    possiamo anche scrivere solo Register senza nulla
    ```

## CREAZIONE SHOW

-   Vediamo ora lo show, quindi innanzitutto andiamo nel resource controller quindi in **ProjectController** e troviamo la show:

```php
 public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }
```

quindi diamo come parametro Project e nel ritorniamo la vista show che creeremo in _views\admin\projects_

**ATTENZIONE:** nel compact passiamo solo project e non projects perchè a noi interessa il dettaglio di un solo project

-   quindi ci creiamo la show nelle views\admin\projects
-   ora andiamo in _index.blade.php_ e ci creiamo il pulsante che porta alla vista show:

```html
<a href="{{ route('admin.projects.show', $project) }}">Show</a>
```

-   ora in _show.blade.php_ ci andiamo a creare il template del dettaglio e ci mettiamo un pulsante per ritornare all'index

-   aggiungiamo un segnaposto _@yild_ in _app.blade.php_ per aggiungere poi la CDN di fontawesome in index e mettiamo l'icone al posto dello show

## CREAZIONE CREATE E STORE

-   Andiamo nel resource controller alla voce create e aggiungiamo questo:

```php
 public function create()
    {
        return view('admin.projects.create');
    }
```

-   in pratica stiamo dicendo che il _create_ ritornerà la vista create in project

-   perciò ci creiamo in _views/admin/projects_ il file _create.blade.php_

-   ci creiamo un pulsante in index.blade.php con la rotta che ci porta alla pagina create.blade.php:

```html
<a href="{{ route('admin.projects.create') }}" class="btn btn-success my-4"
    >Crea progetto</a
>
```

-   facciamo un form che invia i dati allo store in _create.blade.php_

-   e nello store nel resource controller facciamo questo:

```php
  public function store(Request $request)
    {
        // # FACCIAMO UNA VARIABILE DATA CHE RICEVERA' I DATI DEL FORM
        $data = $request->all();

        // # E ISTANZIAMO UN NUOVO OGGETTO CHE CONTERRA' I DATI DEL FORM
        $project = new Project();
        // # ABBIAMO DUE MODI DI FARLO O SINGOLARMENTE PER OGNI VALORE
        // # OPPURE CON IL FILL E METTENDO IL FILLABLE NEL MODEL
        // # IN QUESTO CASO USIAMO IL SECONDO METODO
        // $project->name = $data['name'];
        // $project->link = $data['link'];
        // $project->description = $data['description'];
        // $project->save();

        $project->fill($data);
        $project->save();

        // # FACCIAMO IL REDIRECT IN MANIERA TALE CHE QUANDO SALVIAMO
        // # IL NUOVO PROGETTO CI RIPORTA A UNA ROTTA CHE VOGLIAMO
        return redirect()->route('admin.projects.show', $project);
    }

```

## CREAZIONE EDIT E UPDATING

-   innazitutto ci copiamo il file _create.blade.php_ perchè ci servirà il form
    e lo rinominiamo _edit.blade.php_

```html
<form action="{{ route('admin.projects.update', $project) }}" method="POST">
    @csrf @method('PATCH')
    <div class="row g-3">
        <div class="col-12">
            <label for="name" class="form-label">Name</label>
            <input
                class="form-control"
                type="text"
                id="name"
                name="name"
                value="{{ $project->name }}"
            />
        </div>
        .....
    </div>
</form>
```

-   usiamo il method PATCH e mettiamo i valori vecchi che dovranno essere modificati nei vari input

-   ora andiamo in ProjectController e scriviamo nell'edit:

```php
  public function edit(Project $project)
    {
        // # FACENDO LA Dependency injection QUINDI METTENDO Project $project INVECE DI $ID
        // # CI RISPARMIAMO LA RIGA SOTTO
        // $projects = Project::findOrFail($id);
        return view('admin.projects.edit', compact('project'));
    }
```

-   invece in update:

```php
 public function update(Request $request, Project $project)
    {
        $data = $request->all();
        $project->fill($data);
        $project->save();

        // # COME PER LO STORE FACCIAMO IL REDIRECT IN MANIERA TALE CHE QUANDO SALVIAMO
        // # IL PROGETTO MODIFICATO CI RIPORTA A UNA ROTTA CHE VOGLIAMO
        return redirect()->route('admin.projects.show', $project);
    }
```

-   aggiungiamo anche un tasto nell'index che ci porta all'edit per modificare appunto un progetto

## CREAZIONE DEL DESTROY PER CANCELLARE

-   andiamo nel resource controller alla sezione destroy:

```php
public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.projects.index');
    }
```

-   poi ci creiamo un tasto che elimina nell'index:

```html
  {{-- # IMPORTANTE METTERE # NELL'href --}}
                <a href="#" class="d-inline-block mx-1 text-danger" data-bs-toggle="modal"
                  data-bs-target="#delete-modal-{{ $project->id }}"><i class="fa-solid fa-trash"></i></a>
              </div>
```

-   e ci creiamo una modale con un form per eliminazione questa modale farà comparire un pop up che ci chiederà se vogliamo eliminare o no il file selezionato in maniera tale da non sbagliare

### VALIDAZIONE

### Il metodo `validate`

Innanzitutto bisogna importare il validator nel controller:

```php
// App/Http/Controllers/PastaController.php

use Illuminate\Support\Facades\Validator;
```

Poi creiamo un metodo privato per la logica di validazione in fondo al controller. Nel metodo dobbiamo ricevere i dati da validare

```php
// App/Http/Controllers/PastaController.php

private function validation($data) {

}
```

Nel metodo statico `make` del `Validator`:

-   il primo parametro saranno i dati da validare (array associativo)
-   il secondo parametro saranno le regole di validazione (array associativo)
-   il terzo parametro (opzionale) saranno messaggi di errore customizzati (array associativo)

```php
// App/Http/Controllers/PastaController.php

private function validation($data) {
  Validator::make(
    $data,
    [
      ... regole di validazione
    ],
    [
      ... messaggi di errore
    ]
  )
}
```

Al metodo make dovrà essere concatenato un metodo `->validate()` per eseguire la validazione, ed il risultato sarà ritornato dal nostro metodo privato.

```php
// App/Http/Controllers/PastaController.php

private function validation($data) {
  return Validator::make(
    $data,
    [
      ... regole di validazione
    ],
    [
      ... messaggi di errore
    ]
  )->validate();
}
```

Il risultato finale è questo nel resource controller:

```php
 private function validation($data)
    {
        $validator = Validator::make(
            $data,
            [
                'name' => 'required|string|max:20',
                "description" => "required|string",
                "link" => "required|string",
            ],
            [
                'name.required' => 'Il nome è obbligatorio',
                'name.string' => 'Il nome deve essere una stringa',
                'name.max' => 'Il nome deve massimo di 20 caratteri',

                'description.required' => 'La descrizione è obbligatorio',
                'description.string' => 'La descrizione deve essere una stringa',

                'link.required' => 'Il link è obbligatorio',
                'link.string' => 'Il tipo deve essere una stringa',
            ]
        )->validate();

        return $validator;
    }
```

-   ora andiamo in create:

```php
  {{-- ! QUI METTIAMO NELL'INPUT IL VECCHIO VALORE E IL GLI ERROR PER LA VALIDAZIONE --}}
          <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name"
            value="{{ old('name') }}">
          {{-- ! QUI ABBIAMO IL MESSAGGIO DI ERRORE --}}
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
```

-   invece in edit:

```php
          {{-- ! QUI METTIAMO NELL'INPUT IL VECCHIO VALORE E IL GLI ERROR PER LA VALIDAZIONE --}}
          <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name"
            value="{{ old('name') ?? $project->name }}">
          {{-- ! QUI ABBIAMO IL MESSAGGIO DI ERRORE --}}
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror

```
