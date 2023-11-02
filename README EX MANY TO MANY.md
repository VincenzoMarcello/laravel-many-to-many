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
        foreach ($_technologies as $_technology) {
            $technology = new Technology();
            // # QUI METTIAMO L'ARRAY DI TECNOLOGIE
            $technology->label = $_technology;
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
"technologies" => "nullable|exists:technology,id"
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

# EDIT

-   vediamo ora la edit nel resource controller al metodo edit:

```php
 // # FACCIAMO LO STESSO DEL CREATE PRENDIAMO TUTTE LE TECNOLOGIE E LE MANDIAMO GIU'
        $technologies = Technology::all();

        $technology_ids = $project->technologies->pluck('id')->toArray();

        return view('admin.projects.edit', compact('project', 'types', 'technologies', 'technology_ids'));
```

nelle views dell'edit:

```php
  <!-- # CREIAMOCI UNA CHECK-BOX CON I VALORI DELLE TECNOLOGIE -->
        <div class="col-12">
          <div class="form-check @error('technologies') is-invalid @enderror">
            @foreach ($technologies as $technology)
              <div class="col-2">
                <!-- NEL NAME SI METTONO LE [] PERCHè ALTRIMENTI ANCHE SELEZIONIAMO PIU' CHECKBOX NE ARRIVERà SOLO UNA -->
                <!-- INVECE METTENDO LE [] ARRIVA UN ARRAY CHE CONTIENE TUTTE LE CHECK SEGNATE -->
                <input type="checkbox" name="technologies[]" id="technology-{{ $technology->id }}"
                  value="{{ $technology->id }}" class="form-check-input" @if (in_array($technology->id, old('technologies') ?? $technology_ids)) checked @endif>
                <label for="technology-{{ $technology->id }}">{{ $technology->label }}</label>
              </div>
            @endforeach
          </div>
        </div>
```

mentre nel resource controller al metodo update:

```php
    // # METODO CON VALIDAZIONE
        $data = $this->validation($request->all(), $project->id);
        $project->fill($data);
        $project->save();

        if (array_key_exists('technologies', $data)) {
            $project->technologies()->sync($data["technologies"]);

        } else {
            $project->technologies()->detach();
        }

        // # COME PER LO STORE FACCIAMO IL REDIRECT IN MANIERA TALE CHE QUANDO SALVIAMO
        // # IL PROGETTO MODIFICATO CI RIPORTA A UNA ROTTA CHE VOGLIAMO
        return redirect()->route('admin.projects.show', $project);
```

mentre nel resource controller al metodo destroy:

```php
 // # QUESTO SI FA COME BEST PRACTICE
        $project->technologies()->detach();
```

# STAMPIAMO A SCHERMO I BADGES

Ora facciamo dei badges che si vedano all'interno delle pagine, andiamo nei models e facciamo un getter:

<!-- Project.php -->

```php
    // # QUI FACCIAMO UN GETTER PER PERSONALIZZARE E STAMPARE I BADGES DELLE TECNOLOGIE
    public function getTecnologyBadges()
    {
        $badges_html = "";
        foreach ($this->technologies as $technology) {
            $badges_html .= "<span class='badge rounded-pill mx-1' style='background-color:{$technology->color}'>{$technology->label}</span>";
        }
        return $badges_html;
    }
```

poi andiamo nella views show:

```html
<div class="col-3">
    <p>
        <!-- CI STAMPIAMO I BADGES DELLE TECNOLOGIE CON IL GETTER CHE ABBIAMO FATTO -->
        <strong>Tecnologies:</strong><br />
        {!! $project->getTecnologyBadges() !!}
    </p>
</div>
```

Ora stampiamoli anche nella views dell'index:

```html
<!-- AGGIUNGIAMO UNA COLONNA TECNOLOGIE PER LE TECNOLOGIES -->
.....
<th scope="col">Tecnologie</th>
.....
<!-- USIAMO IL METODO CHE ABBIAMO FATTO ANCHE PER LA SHOW -->
<td>{!! $project->getTecnologyBadges() !!}</td>
.....
```

Se vogliamo popolare le colonne con dati fittizi andiamo nei seeders in ProjectSeeder:

```php
// # CI IMPORTIAMO IL MODELLO TECHNOLOGY
use App\Models\Technology;
```

```php
$technology_ids = Technology::all()->pluck('id');
```

e facciamo l'attach con faker:

```php
// GENERIAMO RANDOMICAMENTE DA 0 A 3 TECNOLOGIE CON IL RAND()
$project->technologies()->attach($faker->randomElements($technology_ids, rand(0, 3)));
```
