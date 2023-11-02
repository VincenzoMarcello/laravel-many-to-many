<?php
// # VISTO CHE ABBIAMO SPOSTATO IL ProjectController
// # DOBBIAMO AGGIUNGERE AL namespace \Admin ALLA FINE
namespace App\Http\Controllers\Admin;

// # E IMPORTARE IL CONTROLLER
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// # IMPORTIAMO IL MODEL Project 
use App\Models\Project;

// # IMPORTIAMO IL MODEL Type
use App\Models\Type;

// # IMPORTIAMO IL MODEL Technology
use App\Models\Technology;

// ! PER LA VALIDAZIONE IMPORTO IL VALIDATOR
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // # PER VEDERLI TUTTI ALL
        // $projects = Project::all();

        // # PER FARE LA PAGINAZIONE E VEDERNE SOLO ALCUNI
        // # QUESTA PARTE DAL PRIMO E ARRIVA ALL'ULTIMO
        // $projects = Project::paginate(9);

        // # FACCIAMO LA PAGINAZIONE ORDINANDO DALL'ULTIMO PROJECT AL PRIMO
        // # TRAMITE IL METODO ORDERBYDESC DELL'id
        $projects = Project::orderByDesc('id')->paginate(9);

        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // # PASSEREMO AL CREATE TUTTI GLI ELEMENTI DI TYPE TRAMITE IL COMPACT E LA FUNZIONE ALL()
        // # PRIMA PERò CI IMPORTIAMO IL MODEL TYPE
        $types = Type::all();
        // # STESSA COSA PER TECHNOLOGY
        $technologies = Technology::all();

        return view('admin.projects.create', compact('types', 'technologies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // # FACCIAMO UNA VARIABILE DATA CHE RICEVERA' I DATI DEL FORM
        // # METODO SENZA VALIDAZIONE
        // $data = $request->all();
        // dd($request->all());
        // # METODO CON VALIDAZIONE
        $data = $this->validation($request->all());

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
        if (array_key_exists('technologies', $data)) {
            $project->technologies()->attach($data["technologies"]);
        }

        // # FACCIAMO IL REDIRECT IN MANIERA TALE CHE QUANDO SALVIAMO
        // # IL NUOVO PROGETTO CI RIPORTA A UNA ROTTA CHE VOGLIAMO
        return redirect()->route('admin.projects.show', $project);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        // # FACENDO LA Dependency injection QUINDI METTENDO Project $project INVECE DI $ID
        // # CI RISPARMIAMO LA RIGA SOTTO
        // $projects = Project::findOrFail($id);
        // # FACCIAMO COME ABBIAMO FATTO NEL CREATE
        $types = Type::all();
        // # FACCIAMO LO STESSO DEL CREATE PRENDIAMO TUTTE LE TECNOLOGIE E LE MANDIAMO GIU'
        $technologies = Technology::all();

        $technology_ids = $project->technologies->pluck('id')->toArray();

        return view('admin.projects.edit', compact('project', 'types', 'technologies', 'technology_ids'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {

        // # METODO SENZA VALIDAZIONE
        // $data = $request->all();

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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        // # QUESTO SI FA COME BEST PRACTICE
        $project->technologies()->detach();

        $project->delete();
        return redirect()->route('admin.projects.index');
    }

    // ! FACCIO UN METODO PRIVATO PER LA VALIDAZIONE
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
                "type_id" => "nullable|exists:types,id",
                "technologies" => "nullable|exists:technologies,id"
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
                'type_id.exists' => 'Il tipo inserito non è valido',
                'technologies.exists' => 'Le tecnologie inserite non sono valido',

            ]
        )->validate();

        return $validator;
    }
}
