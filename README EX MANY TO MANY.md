# TRACCIA

Continuiamo a lavorare sul codice dei giorni scorsi, ma in una nuova repo e aggiungiamo una nuova entità Technology. Questa entità rappresenta le tecnologie utilizzate ed è in relazione many to many con i progetti.
I task da svolgere sono diversi, ma alcuni di essi sono un ripasso di ciò che abbiamo fatto nelle lezioni dei giorni scorsi:

-   creare la migration per la tabella technologies
-   creare il model Technology
-   creare la migration per la tabella pivot project_technology
-   aggiungere ai model Technology e Project i metodi per definire la relazione many to many
-   visualizzare nella pagina di dettaglio di un progetto le tecnologie utilizzate, se presenti
-   permettere all'utente di associare le tecnologie nella pagina di creazione e modifica di un progetto
-   gestire il salvataggio dell'associazione progetto-tecnologie con opportune regole di validazione

# Bonus 1:

    creare il seeder per il model Technology.

# Bonus 2:

    aggiungere le operazioni CRUD per il model Technology, in modo da gestire le tecnologie utilizzate nei progetti direttamente dal pannello di amministrazione.

# SVOLGIMENTO

# CREAZIONE MIGRATION,MODEL AND SEEDER

-   partiamo con il crearci il model, migration e seeder di Technology:

```
php artisan make:model Technology -ms
```

# MIGRATION

-   ora andiamo nelle migration in create_technologies_table e aggiungiamo le colonne:

```php
   public function up()
    {
        Schema::create('technologies', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->char('color', 7);
            $table->timestamps();
        });
    }

    sarà uguale all'esercizio di one to many
```

# SEEDER

-   Ora andiamo nel seeder precisamente TechnologySeeder:

```php

// # IMPORTIAMO IL MODELLO TECHNOLOGY
use App\Models\Technology;

// # CI IMPORTIAMO FAKER
use Faker\Generator as Faker;


   public function run(Faker $faker)
    {
        // # FACCIAMO UN ARRAY DI TECNOLOGIE METTO "_" PER DIFFERENZIARLA DALLA VARIABILE SOTTO
        // # CHE CONTERRA' TECHNOLOGY()
        $_technologies = ["html", "css", "js", "sass", "vue", "php", "mysql", "laravel"];

        // # QUI FACCIAMO IL CICLO PER POPOLARE OGNI ELEMENTO DEL DB
        foreach ($_technologies as $_technologies) {
            $technology = new Technology();
            // # QUI METTIAMO L'ARRAY DI TECNOLOGIE
            $technology->label = $_technologies;
            // # QUI GENERIAMO UN COLORE CASUALE IN ESADECIAMALE
            $technology->color = $faker->hexColor();
            $technology->save();
        }
    }
```

# CREAZIONE TABELLA PONTE O PIVOT

-   ora dobbiamo collegare la tabella del tecnologie con la tabella dei progetti quindi ci facciamo la tabella ponte, quindi facciamo una terza migration:

```
php artisan make:migration create_project_technology_table
```

-   andiamo in create_project_technology_table e ci creiamo le due chiavi secondarie:

```php
  public function up()
    {
        Schema::create('project_technology', function (Blueprint $table) {
            $table->id();
            // # CI TOGLIAMO IL TIMESTAMP CHE NON CI SERVE E METTIAMO LE DUE FOREIGN ID
            // # IN QUESTO CASO IL CASCADEONDELETE ELIMINA SOLO LA RELAZIONE CHE HA IL PROJECT CON LA TECNOLOGIA
            // # PERCHè SIAMO NELLA TABELLA PONTE QUINDI SE ELIMINIAMO UN PROJECT SI ELIMINERA' LA RELAZIONE
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technology_id')->constrained()->cascadeOnDelete();
        });
    }
```

-   mettiamo TechnologySeeder nel DatabaseSeeder.php e facciamo un refresh --seed

# MODELS

-   ora spostiamoci sui Models e andiamo a dire che esiste una relazione:

-   in Project:

```php
 public function technologies()
    {
        // # QUI STIAMO DICENDO CHE QUESTI (PROGETTI) APPARTENGONO A MOLTE TECNOLOGIE
        return $this->belongsToMany(Technology::class);
    }
```

-   in Technology facciamo il contrario:

```php
 public function projects()
    {
        // # QUI STIAMO DICENDO CHE A QUESTE (TECNOLOGIE) APPARTENGONO A MOLTE PROGETTI
        return $this->belongsToMany(Project::class);
    }
```

# ANDIAMO A VEDERE LE CRUD

-   ora andiamo in create e stampiamo a schermo delle check-box con i valori delle tecnologie

# CREATE

-   andiamo nel resource controller:

```php
// # IMPORTIAMO IL MODEL Technology
use App\Models\Technology;


 public function create()
    {
        // # PASSEREMO AL CREATE TUTTI GLI ELEMENTI DI TYPE TRAMITE IL COMPACT E LA FUNZIONE ALL()
        // # PRIMA PERò CI IMPORTIAMO IL MODEL TYPE
        $types = Type::all();
        // # STESSA COSA PER TECHNOLOGY
        $technologies = Technology::all(); <--------

        return view('admin.projects.create', compact('types', ----->'technologies'<-----));
    }
```

-   poi andiamo in views\admin\projects\create:

```php
     //  CREIAMOCI UNA CHECK-BOX CON I VALORI DELLE TECNOLOGIE
        <div class="col-12">
          <div class="row">
            @foreach ($technologies as $technology)
              <div class="col-2">
                //  NEL NAME SI METTONO LE [] PERCHè ALTRIMENTI ANCHE SELEZIONIAMO PIU' CHECKBOX NE ARRIVERà SOLO UNA
                // INVECE METTENDO LE [] ARRIVA UN ARRAY CHE CONTIENE TUTTE LE CHECK SEGNATE
                <input type="checkbox" name="technologies[]" id="technology-{{ $technology->id }}"
                  value="{{ $technology->id }}" class="form-check-control">
                <label for="technology-{{ $technology->id }}">{{ $technology->label }}</label>
              </div>
            @endforeach
          </div>
        </div>
```

-   ora dobbiamo solo validare i dati che ci arrivano dalla check-box quindi nel ProjectController nel metodo Validation:

```php
.....
"technologies" => "nullable|integer|exists:technology,id"
.....
'technologies' => 'Le tecnologie inserite non sono valido',
....
```

-   ora andiamo al metodo store:

```php
        ......
        $project->technologies()->attach($data["technologies"]);

        // # FACCIAMO IL REDIRECT IN MANIERA TALE CHE QUANDO SALVIAMO
        // # IL NUOVO PROGETTO CI RIPORTA A UNA ROTTA CHE VOGLIAMO
        return redirect()->route('admin.projects.show', $project);
        ......
```

-   aggiungiamo un controllo se non checkiamo nessuna tecnologia infatti ci darà errore per evitare questo errore:3

```php
  if (array_key_exists('technologies', $data)) {
            $project->technologies()->attach($data["technologies"]);
        }
```

-   e nella views create aggiungiamo la checkbox:

```php
    // CREIAMOCI UNA CHECK-BOX CON I VALORI DELLE TECNOLOGIE
        <div class="col-12">
          <div class="form-check @error('technologies') is-invalid @enderror">
            @foreach ($technologies as $technology)
              <div class="col-2">
                // NEL NAME SI METTONO LE [] PERCHè ALTRIMENTI ANCHE SELEZIONIAMO PIU' CHECKBOX NE ARRIVERà SOLO UNA
                // INVECE METTENDO LE [] ARRIVA UN ARRAY CHE CONTIENE TUTTE LE CHECK SEGNATE
                <input type="checkbox" name="technologies[]" id="technology-{{ $technology->id }}"
                  value="{{ $technology->id }}" class="form-check-input" @if (in_array($technology->id, old('technologies') ?? [])) checked @endif>
                <label for="technology-{{ $technology->id }}">{{ $technology->label }}</label>
              </div>
            @endforeach
          </div>
        </div>
```
