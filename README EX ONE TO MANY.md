# TRACCIA

Continuiamo a lavorare sul codice dei giorni scorsi, ma in una nuova repo e aggiungiamo una nuova entità **Type**. Questa entità rappresenta la tipologia di progetto ed è in relazione one to many con i progetti.
I task da svolgere sono diversi, ma alcuni di essi sono un ripasso di ciò che abbiamo fatto nelle lezioni dei giorni scorsi:

-   creare la migration per la tabella types
-   creare il model Type
-   creare la migration di modifica per la tabella projects per aggiungere la chiave esterna
-   aggiungere ai model Type e Project i metodi per definire la relazione one to many
-   visualizzare nella pagina di dettaglio di un progetto la tipologia associata, se presente
-   permettere all'utente di associare una tipologia nella pagina di creazione e modifica di un progetto
-   gestire il salvataggio dell'associazione progetto-tipologia con opportune regole di validazione
    Bonus 1:
    creare il seeder per il model Type.
    Bonus 2:
    aggiungere le operazioni CRUD per il model Type, in modo da gestire le tipologie di progetto direttamente dal pannello di amministrazione.

# SVOLGIMENTO

## CREAZIONE TABELLA types CON Migration e seeder

-   Innazitutto ci dobbiamo creare la nuova tabella _types_ quindi il model e la migration e il seeder per popolarla:

```
php artisan make:model Type -ms

Così in un colpo solo ci creiamo model,migration e seeder
```

## MIGRATION

-   poi andiamo in migrations\create_types_table.php e ci creiamo le colonne della tabella:

```php
  public function up()
    {
        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->string('label', 20);
            // QUI METTIAMO 7 PERCHE' USIAMO L'ESADECIMALE PER IL COLORE
            $table->char('color', 7);
            $table->timestamps();
        });
    }
```

-   buttiamo giù le tabelle con:

```
php artisan make:reset
```

-   perchè dobbiamo aggiungere la chiave secondaria per fare il collegamento quindi ci creiamo una nuova migration:

```
php artisan make:migration add_foreign_type_id_to_projects_table
```

-   andiamo nella migration appena creata e ci creiamo colonna e relazione:

```php
 public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('type_id')->constrained();  <----
        });
    }

    foreignId('type_id') colonna
    constrained() relazione
```

-   possiamo arricchire questa funzione aggiungendo:

```php
 $table->foreignId('type_id')->nullable()->after('id')->constrained()->nullOnDelete();

 nullable() qui stiamo dicendo che un progetto potrebbe anche non avere un tipo
 after('id') qui stiamo dicendo che la colonna "type_id" sarà piazzata dopo la colonna "id"
 nullOnDelete() qui stiamo dicendo se il tipo viene cancellato metti null al progetto
 questa va in combo con nullable()
```

-   Ora nel metodo down dobbiamo droppare la relazione e la colonna in maniera tale che se facciamo un reset o un refresh non darà errore:

```php
 public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            // # DROP RELAZIONE
            $table->dropForeign('projects_type_id_foreign');
            // # DROP COLONNA
            $table->dropColumn('type_id');
        });
    }

    RICORDA: Se non droppi prima la relazione non riuscirai a droppare la colonna
```

-   facciamo un migrate per aggiungere la relazione e la colonna:

```
php artisan migrate
```

-   possiamo provare a fare un reset o un refresh per vedere se funziona tutto senza errori

## SEEDER

-   ora dobbiamo fare il seeder andiamo in seeders e apriamo il TypeSeeder:

```php
// # CI IMPORTIAMO IL MODELLO TYPE
use App\Models\Type;

// # CI IMPORTIAMO FAKER
use Faker\Generator as Faker;
```

```php
   public function run(Faker $faker)
    {
        // # CI CREIAMO UNA VARIABILE CHE CONTIENE UN ARRAY DI TYPE
        $_types = [
            "Front-end",
            "Back-end",
            "Full Stack"
        ];

        // # FACCIAMO UN CICLO E PER OGNI ELEMENTO DEL DB:
        foreach ($_types as $_type) {
            // # ABBIAMO UN OGGETTO TYPE
            $type = new Type();
            // # CHE CONTERRA' UNA LABEL CHE CONTIENE L'ARRAY DI TYPE
            $type->label = $_type;
            // # E UN COLORE GENERATO CASUALMENTE IN ESADECIMALE DA FAKER
            $type->color = $faker->hexColor();
            // # SALVIAMO NEL DB
            $type->save();
        }
    }
```

-   aggiungiamo al DatabaseSeeder.php anche:

```php
 $this->call([
            TypeSeeder::class, <-----
            ProjectSeeder::class,
            UserSeeder::class
        ]);

in maniera tale che quando facciamo il seed verrà eseguito anche TypeSeeder
```

-   ora in ProjectSeeder.php:

```php
// # CI IMPORTIAMO IL MODELLO TYPE
use App\Models\Type;
```

```php
public function run(Faker $faker)
    {
        // # CI FACCIAMO UN VARIABILE CHE PRENDE TUTTI GLI OGGETTI TYPE E CON IL pluck('id)
        // # ANDIAMO A PRENDERE TUTTI GLI ID NEL TYPE QUINDI SI CREERA' UN ARRAY DI ID
        $type_ids = Type::all()->pluck('id');
        // # SICCOME VOGLIAMO ANCHE DEI VALORI NULL ANDIAMO AD AGGIUNGERE ALL'ARRAY DI ID
        // # ANCHE UN VALORE NULL [1,2,3,..,null]
        $type_ids[] = null;

        for ($i = 0; $i < 50; $i++) {
            $project = new Project();
            // # CON IL METODO randomElement($type_ids) di FAKER CI ANDIAMO A PRENDERE DEGLI ID
            // # CASUALI DALL'ARRAY DI ID
            $project->type_id = $faker->randomElement($type_ids);
            ......
        }}
```

-   ora facciamo un refresh --seed

# STAMPARE A SCHERMO LE RELAZIONE

-   ora proviamo a stamparla la relazione

## MODEL

-   prima cosa da fare e andare nei Models e aggiungere le funzioni belongsTo() e hasMany()
    in quanto abbiamo una relazione one to many un type che ha molti projects ma un project ha solo un type:

-   andiamo in Project nei Models:

```php
  // # QUI STIAMO FACENDO UN PUBLIC FUNCTION CHE DICE CHE UN PROJECT HA UN SOLO TYPE
    public function type()
    {
        // # PER QUESTO INTENDIAMO PROJECT E TRADOTTO
        // # 'PROJECT'->'APPARTIENE A'('TYPE')
        return $this->belongsTo(Type::class);

        // # QUINDI ORA DA PROJECT POSSIAMO ACCEDERE A TYPE
    }
```

-   ora facciamo il contrario sul Models Type:

```php
 // # QUI STIAMO FACENDO UN PUBLIC FUNCTION CHE DICE CHE UN TYPE HA MOLTI PROJECT
    // ! QUINDI METTIAMO PROJECTS AL PLURALE
    public function projects()
    {
        // # PER QUESTO INTENDIAMO TYPE E TRADOTTO
        // # 'TYPE'->'HA MOLTI'('PROJECT')
        return $this->hasMany(Project::class);

        // # QUESTO METODO PROJECTS CI RESTITUIRA' UN ARRAY
    }

```

-   proviamo a stampare nella views show:

```html
<div class="col-4">
    <p>
        <!-- QUI STAMPIAMO LA LABEL DEL TYPE CHE ABBIAMO COLLEGATO TRAMITE
        RELAZIONE  USIAMO IL '?' CHE E' IL NULL OPERATOR IN MANIERA
        TALE CHE SE CAPITA SENZA TYPE NON DARA' ERRORE MA DARA' IL
        CAMPO VUOTO, POI PER FARE UNA COSA MIGLIORE POSSIAMO FARE UNA FUNZIONE
        GETTER CHE METTE TIPO UNTYPE O QUELLO CHE VOGLIAMO NOI -->
        <strong>Type:</strong><br />
        {{ $project->type?->label }}
    </p>
</div>
```

# STAMPA DI BADGE CON IL TYPE NELLO SHOW

-   proviamo a fare un getter per fare le cose più carine andiamo in Models Project:

```php
public function getTypeBadge()
    {
        // # FACCIAMO UN TERNARIO CHE DICE CHE SE CI STA UN TIPO ALLORA STAMPIAMO UN BADGE
        // # CON IL COLORE PRESO DA TYPE E LA LABEL PRESA DA TYPE
        // # SE IL TYPE é NULL ALLORA STAMPIAMO UNTYPE
        // return $this->type ? "<span class='badge' style='background-color:{$this->type->color}'>{$this->type->label}</span>" : 'Untype';

        // # PERSONALIZZIAMO ANCHE L'UNTYPE
        return $this->type ? "<span class='badge' style='background-color:{$this->type->color}'>{$this->type->label}</span>" : "<span class='badge text-bg-danger'>Untype</span>";
    }
```

-   proviamo a stampare nella views show con il metodo apposto di quello di prima:

```html
<div class="col-4">
    <p>
        <!--I DOPPI PUNTI !! SONO PER STAMPARE L'HTML DEL TERNARIO ALTRIMENTI
        STAMPEREBBE L'HTML LETTERALMENTE -->
        <strong>Type:</strong><br />
        {!! $project->getTypeBadge() !!}
    </p>
</div>
```

# STAMPA DI BADGE CON IL TYPE NEL' INDEX

-   stampiamo anche nell'index:

```html
<!-- AGGIUNGIAMO UN COLONNA TIPO PER IL TYPE -->
......
<th scope="col">Tipo</th>
.....
<!-- ! USIAMO IL METODO CHE ABBIAMO FATTO ANCHE PER LA SHOW -->
<td>{!! $project->getTypeBadge() !!}</td>
.....
```

# COME SELEZIONARE IL BADGE CON IL TYPE NEL CREATE

-   ora facciamo un select nel create per aggiungere un type a un nuovo project:

```php
// # IMPORTIAMO IL MODEL Type
use App\Models\Type;

public function create()
    {
        // # PASSEREMO AL CREATE TUTTI GLI ELEMENTI DI TYPE TRAMITE IL COMPACT E LA FUNZIONE ALL()
        $types = Type::all();
        return view('admin.projects.create', compact('types'));
    }
```

-   ora anggiungiamo il type_id al fillable nel Model Project:

```php
 protected $fillable = [
        "name",
        "link",
        "description",
        "type_id" <---
    ];
```

-   aggiungiamo la validazione nel resource controller:

```php
 private function validation($data)
    {
        $validator = Validator::make(
            $data,
            [
                'name' => 'required|string|max:20',
                "description" => "required|string",
                "link" => "required|string",
                // # QUI STIAMO DICENDO CHE PUO ESSERE NULLO E CHE IL TYPE DEVE ESISTERE NEL CAMPO DELL'ID
                // # QUINDI SE ABBIAMO 10 ID E METTIAMO 12 CI DARA' ERRORE
                "type_id" => "nullable|integer|exists:types,id" <-----
            ],
            [
                'name.required' => 'Il nome è obbligatorio',
                'name.string' => 'Il nome deve essere una stringa',
                'name.max' => 'Il nome deve massimo di 20 caratteri',

                'description.required' => 'La descrizione è obbligatorio',
                'description.string' => 'La descrizione deve essere una stringa',

                'link.required' => 'Il link è obbligatorio',
                'link.string' => 'Il tipo deve essere una stringa',
                // # QUI METTIAMO IL MESSAGGIO DELL'ERRORE
                'type_id.exists' => 'Il tipo inserito non è valido', <-----

            ]
        )->validate();

        return $validator;
    }
```

-   Ora siamo pronti a fare la select perchè abbiamo tutti i pezzi:

```html
<!-- FACCIAMO UNA SELECT PER SCEGLIERE IL TIPO USEREMO LA CHIAVE SECONDARIA -->
<div class="col-12">
    <label for="type_id" class="form-label">Tipo</label>
    <select
        name="type_id"
        id="type_id"
        class="form-select @error('type_id') is-invalid @enderror"
    >
        Seleziona un Tipo
        <option value="">Nessun Tipo</option>
        <!-- QUI FACCIAMO UN CICLO CON GLI ELEMENTI CHE CI ARRIVANO DAL CREATE DEL ProjectController -->
        @foreach ($types as $type)
        <option value="{{ $type->id }}">{{ $type->label }}</option>
        @endforeach
    </select>

    @error('type_id')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
```

-   vai nel create per approfondire.

# COME MODIFICARE IL BADGE CON IL TYPE DI UN PROGETTO ESISTENTE

-   andiamo nell'edit:

```php
 public function edit(Project $project)
    {
        // # FACCIAMO COME ABBIAMO FATTO NEL CREATE E PASSIAMO IL TYPES NEL COMPACT
        $types = Type::all(); <----
        return view('admin.projects.edit', compact('project','types'));
    }
```

-   ora possiamo usare la stessa select del create anche nel edit:

```php
    <div class="col-12">
             <label for="type_id" class="form-label">Tipo</label>
             <select name="type_id" id="type_id" class="form-select @error('type_id') is-invalid @enderror">Seleziona un
               Tipo
               <option value="">Nessun Tipo</option>
               <option value="100" @if (old('type_id') == '100') selected @endif>Non Valido</option>
            // CAMBIANDO PERò QUESTA PARTE DI CODICE
               @foreach ($types as $type)
                 <option value="{{ $type->id }}" @if (old('type_id') && $type->type && old('type_id') == $type->type->id) selected @endif>{{ $type->label }}
                 </option>
               @endforeach
             </select>

             @error('type_id')
               <div class="invalid-feedback">{{ $message }}</div>
             @enderror
           </div>
```
