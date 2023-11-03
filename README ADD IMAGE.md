# TRACCIA

Continuiamo a lavorare nella repo dei giorni scorsi e aggiungiamo un'immagine ai nostri progetti.
Ricordiamoci di creare il symlink con l'apposito comando artisan e di aggiungere l'attributo enctype="multipart/form-data" ai form di creazione e di modifica!

# Bonus

Implementare l'invio di una mail

# SVOLGIMENTO

per caricare un'immagine o un file abbiamo bisogno di uno spazio in cui caricarlo, il path che associa il file a ogni posts e un form da cui un utente caricherà il file.

Lo spazio dove salvarlo si trova nella cartella storage, questa avrà un local disk che non è accessibile dal web e un public disk che invece è accessibile dal web tramite URL, quindi le immagini salvate in local non saranno accessibili dal web mentre quelle salvate in public si.

andiamo ora a scegliere il disco da utilizzare se locale o pubblico, andiamo nel file .env:

```php
...
FILESYSTEM_DISK=public

// oppure

FILESYSTEM_DISK=local

// QUI DECIDIAMO SE VOGLIAMO IL DISCO PUBBLICO O LOCALE
...
```

poi andiamo nella cartella config in filesystems.php:

```php
// QUI IN PRATICA CI STA DICENDO CHE SE ABBIAMO IL FILESYSTEM_DISK IN .env USERA'
// L'OPZIONE CHE ABBIAMO MESSO IN .env ALTRIMENTI DI DEFAULT USERA' IL VALORE DOPO LA VIRGOLA
// IN QUESTO CASO LOCAL, PERò SICCOME NOI ABBIAMO MESSO NEL FILE .env 'public' USERA' QUELLO

 'default' => env('FILESYSTEM_DISK', 'local'),

```

Ora dobbiamo fare un collegamento tra la cartella public esterna (quella che sta sotto node_modules o lang) e la cartella public che sta in storage, useremo questo comando per creare il **symlink**:

```
php artisan storage:link
```

questo crea un collegamento "storage" nella cartella public esterna che porta alla cartella public interna quindi qualsiasi file metteremo nella cartella public comparirà in entrambe le cartelle public.

ora ci andiamo a fare una nuova migration in cui per ogni project salveremo un cover image potremo anche aggiungere una colonna direttamente alla migration che abbiamo ma ora la facciamo apparte:

```
php artisan make:migration add_cover_image_to_projects_table
```

poi andiamo in questa migration appena creata e ci creiamo una nuova colonna nella tabella projects del DB che per salvare il path di quest'immagine quindi:

```php
 public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            // # CI ANDIAMO A CREARE UNA NUOVA COLONNA NEL DB NELLA TABELLA PROJECTS
            // # IN MANIERA TALE DA POTER SALVARE IL PATH DELL'IMMAGINE PER QUESTO SARA' UNA STRINGA
            // # METTIAMO NULLABLE IN QUANTO UN PROJECT POTREBBE ANCHE NON AVERE UN'IMMAGINE
            // # E L'AFTER PER DECIDERE DOVE VERRA' POSIZIONATA LA COLONNA IN QUESTO CASO DOPO NAME
            $table->string('cover_image')->nullable()->after('name');
        });
    }
```

```php
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            // # QUI METTIAMO IL DROP DELLA COLONNA IN MANIERA TALE CHE SE FACCIAMO UN RESET
            // # O ALTRO, SI CANCELLERA' SENZA DARE ERRORE
            $table->dropColumn('cover_image');
        });
    }
```

e facciamo:

```php
php artisan migrate
<!-- in maniera tale da aggiungere la colonna al DB -->
```
