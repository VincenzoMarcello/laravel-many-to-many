# TRACCIA

Continuiamo a lavorare nella repo dei giorni scorsi e aggiungiamo un'immagine ai nostri progetti.
Ricordiamoci di creare il symlink con l'apposito comando artisan e di aggiungere l'attributo enctype="multipart/form-data" ai form di creazione e di modifica!

# Bonus

Implementare l'invio di una mail

# SVOLGIMENTO

per caricare un'immagine o un file abbiamo bisogno di uno spazio in cui caricarlo, il path che associa il file a ogni posts e un form da cui un utente caricherà il file.

## SCEGLIAMO IL DISCO SE PUBBLICO O LOCALE

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

## FACCIAMO UN COLLEGAMENTO TRA LE DUE CARTELLE PUBLIC

Ora dobbiamo fare un collegamento tra la cartella public esterna (quella che sta sotto node_modules o lang) e la cartella public che sta in storage, useremo questo comando per creare il **symlink**:

```
php artisan storage:link
```

questo crea un collegamento "storage" nella cartella public esterna che porta alla cartella public interna quindi qualsiasi file metteremo nella cartella public comparirà in entrambe le cartelle public.

## FACCIAMO UNA NUOVA MIGRATION PER AGGIUNGERE UNA NUOVA COLONNA ALLA TABELLA PROJECTS

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

## ANDIAMO A VEDERE ORA IL FORM DOVE CARICHEREMO IL FILE O L'IMAGE

ora andiamo nel form e vediamo che succede quando effettivamente carichiamo un image o un file.

**ATTENZIONE** i form normalmente non sono abilitati all'invio dei file per farlo dobbiamo aggiungere `enctype="multipart/form-data"`, in quanto se inviassimo senza questa cosa arriverebbe solo una stringa e non il file fisico:

## PARTIAMO DAL CREATE

<!-- in views create -->

```html
<form ... enctype="multipart/form-data"></form>

<!-- AL FORM VA AGGIUNTA QUESTA PARTE enctype="multipart/form-data" E SI ABILITA IL SALVATAGGIO DEL FILE -->
```

```html
<!-- LA RIGA DELL'INPUT SARA' UGUALE ALLE ALTRE MA AVRA' type=file -->
<div class="col-12">
    <label for="cover_image" class="form-label">Scegli immagine</label>
    <input
        class="form-control @error('cover_image') is-invalid @enderror"
        type="file"
        id="cover_image"
        name="cover_image"
        value="{{ old('cover_image') }}"
    />
    @error('cover_image')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
```

## PER QUANTO RIGUARDA LA VALIDAZIONE

nel resource controller al metodo validation:

```php
// # QUI ANDIAMO A SPECIFICARE CHE ESTENSIONI ACCETTIAMO PER L'IMMAGINE IN QUESTO CASO TUTTE
                // # QUELLE CHE POSSONO ESSERE IMMAGINI, OPPURE POTEVAMO DARE mimes:jpg,png,bmp,...
                // # OPPURE SE NON ERA UN'IMMAGINE MA UN FILE QUALSIASI POTEVAMO METTERE file
                // # METTIAMO max:1024 PER DIRE CHE L'IMMAGINE DEVE ESSERE MASSIMO DI 1024KB
                'cover_image' => 'nullable|image|max:1024',
                .....
                'cover_image.image' => "Il file caricato deve essere un'immagine",
                'cover_image.max' => "Il file caricato non deve superare i 1024KB",
                .....
```

## PER QUANTO RIGUARDA LO STORE

ora per salvare l'immagine dobbiamo passare per lo store nel resource controller, innanzitutto ci dobbiamo salvare la facades Storage:

```php
use Illuminate\Support\Facades\Storage;
```

nel metodo store del resource controller:

```php
 // # METTIAMO L'IMMAGINE IN UNA CARTELLA TRAMITE LO STORAGE E QUELLO CHE CI ARRIVA (put)
        $cover_image_path = Storage::put('cartella in cui caricare il file', $data['cover_image']);
        // # NEL DB METTIAMO IL PATH
        $project->cover_image = $cover_image_path;
```

```php
// SCEGLIAMO IL NOME DELLA CARTELLA, FACCIAMO COSì IN QUANTO SE ABBIAMO PIU' IMMAGINI LE POSSIAMO RAGGRUPPARE
// E NON AVERLE SPARSE PERCIò LO SCAFFOLDING CI VUOLE
$cover_image_path = Storage::put('upload/projects/cover_image', $data['cover_image']);
// ESEMPIO PIU' IMMAGINI
$cover_image_path = Storage::put('upload/projects/cover_a', $data['cover_a']);
$cover_image_path = Storage::put('upload/projects/cover_b', $data['cover_b']);
$cover_image_path = Storage::put('upload/users/user_pic', $data['user_pic']);
```

andiamo a mettere un controllo se inviamo il form senza caricare nessun'immagine altrimenti ci darà errore:

```php
 if (array_key_exists('cover_image', $data)) {   <--------
        $cover_image_path = Storage::put('cartella in cui caricare il file', $data['cover_image']);
        $project->cover_image = $cover_image_path;
 }
```

## VISUALIZZARE L'IMMAGINE NELLA SHOW

ora vogliamo visualizzare nella views show l'immagine:

```php
// USIAMO IL METODO ASSET E SOMMIAMO IL PATH /storage/ CON $project->cover_image
   <div class="col-12">
        <p>
          <strong>Cover imgage</strong><br>
          <img src="{{ asset('/storage/' . $project->cover_image) }}" class="img-fluid" alt="">
        </p>
      </div>
```

## DESTROY

dopo di che in destroy nel resource controller andiamo a mettere
un if che cancella l'immagine salvata nello storage se cancelliamo il project a cui è associata:

```php
        // # QUI METTIAMO UN IF CHE ELIMINA L'IMMAGINE DALLO STORAGE SE CI STA
        // # SE NON METTESSIMO QUESTO QUANDO ELIMINIAMO UN PROJECT L'IMMAGINE SALVATA FISICAMENTE
        // # NELLO STORAGE RIMARREBBE MENTRE METTENDO QUESTO VIENE CANCELLATA INSIEM AL PROJECT
        // # QUINDI if SE ESISTE L'IMMAGINE USA IL METODO DI STORAGE CHE ELIMINA E CANCELLA L'IMMAGINE
        if ($project->cover_image) {
            Storage::delete($project->cover_image);
        }
```

## ORA MANCA LA MODIFICA

andiamo nell'edit nelle views:
