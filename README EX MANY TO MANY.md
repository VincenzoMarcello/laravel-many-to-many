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

-   partiamo con il crearci il model, migration e seeder di Technology:

```
php artisan make:model Technology -ms
```

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
