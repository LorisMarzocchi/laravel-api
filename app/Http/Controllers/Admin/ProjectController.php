<?php

namespace App\Http\Controllers\Admin;

use App\Models\Type;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{

    private $validation = [
        'title'          => 'required|string|max:50',
        'type_id'        => 'required|integer|exists:types,id',
        'image'          => 'nullable|image|max:2048',
        'description'    => 'required|string',
        'link_github'    => 'required|url|max:150',
        'technologies'   => 'nullable|array',
        'technologies.*' => 'integer|exists:technologies,id',

    ];
    private $validation_messages = [
        'required'      => 'il campo :attribute è obbligatorio', // per personalizzare il messaggio di errore
        'min'           => 'il campo :attribute deve avere :min carattri',
        // 'max'           => 'il campo :attribute deve avere :max carattri',
        'url'           => 'il campo è obbligatorio',
        'exists'        => 'Valore non accetato',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::with('type', 'technologies')->paginate(5);
        // $projects = Project::paginate(5);
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types              = Type::all();
        $technologies       = Technology::all();
        return view('admin.projects.create', compact('types', 'technologies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->validation, $this->validation_messages);

        $data = $request->all();

        //salvare immagine in upload
        //prendere percorso immagine

        if ($request->hasFile('image')) {
            // Se l'immagine è stata caricata, la salvo e otteniamo il percorso
            $imagePath = Storage::put('uploads', $data['image']);
        } else {
            // Altrimenti, un valore di default
            $imagePath = 'defaultImage/default.jpg';
        }

        //salvare immagine

        $newProject = new Project();
        $newProject->title          = $data['title'];
        $newProject->slug           = $newProject::slugger($data['title']);
        $newProject->type_id        = $data['type_id'];
        $newProject->image          = $imagePath;
        $newProject->description    = $data['description'];
        $newProject->link_github    = $data['link_github'];
        $newProject->save();

        $newProject->technologies()->sync($data['technologies'] ?? []);

        return redirect()->route('admin.projects.show', ['project' => $newProject]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $project = Project::with('type', 'technologies')->where('slug', $slug)->firstOrFail();
        // $project = Project::where('slug', $slug)->firstOrFail();
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $project            = Project::where('slug', $slug)->firstOrFail();
        $types              = Type::all();
        $technologies       = Technology::all();

        return view('admin.projects.edit', compact('project', 'types', 'technologies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();


        $request->validate($this->validation, $this->validation_messages);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // salvare l'immagine nuova
            $imagePath = Storage::put('uploads', $data['image']);

            // eliminare l'immagine vecchia
            if ($project->image) {
                Storage::delete($project->image);
            }

            // aggiormare il valore nella colonna con l'indirizzo dell'immagine nuova
            $project->image = $imagePath;
        }

        $project->title         = $data['title'];
        $project->type_id       = $data['type_id'];
        $project->description   = $data['description'];
        $project->link_github   = $data['link_github'];
        $project->update();

        $project->technologies()->sync($data['technologies'] ?? []);

        return redirect()->route('admin.projects.show', ['project' => $project]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();

        $project->delete();

        return to_route('admin.projects.index')->with('delete_success', $project);
    }

    public function restore($slug)
    {
        $project = Project::find($slug);

        Project::withTrashed()->where('slug', $slug)->restore();
        $project = Project::where('slug', $slug)->firstOrFail();

        return to_route('admin.projects.trashed')->with('restore_success', $project);
    }

    public function trashed()
    {
        $trashedProjects = Project::onlyTrashed()->paginate(5);

        return view('admin.projects.trashed', compact('trashedProjects'));
    }

    public function harddelete($slug)
    {
        $project = Project::withTrashed()->where('slug', $slug)->first();

        if ($project->image) {
            Storage::delete($project->image);
        }

        $project->technologies()->detach();
        $project->forceDelete();
        return to_route('admin.projects.trashed')->with('delete_success', $project);
    }
}
