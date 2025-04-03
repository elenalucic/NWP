<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('user_id', Auth::id())
            ->orWhereHas('members', function ($query) {
                $query->where('user_id', Auth::id());
            })->with('tasks')->get();
    
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $project = new Project();
        $project->name = $request->name;
        $project->description = $request->description;
        $project->price = $request->price;
        $project->start_date = $request->start_date;
        $project->end_date = $request->end_date;
        $project->user_id = Auth::id();
        $project->save();

        if ($request->has('members')) {
            $project->members()->attach($request->input('members'));
        }

        return redirect()->route('projects.index')->with('success', 'Projekt uspješno kreiran!');
    }

    public function edit(Project $project)
    {
        $project->load('tasks'); 
        return view('projects.edit', compact('project'));
    }
    public function update(Request $request, Project $project)
    {
        if (Auth::id() !== $project->user_id && !$project->members->contains(Auth::id())) {
            abort(403, 'Nemate ovlasti za uređivanje ovog projekta.');
        }

        if (Auth::id() === $project->user_id) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);
            $project->update($validated);
        }

        return redirect()->route('projects.index')->with('success', 'Projekt uspješno ažuriran!');
    }

    public function destroy(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Projekt obrisan!');
    }

    public function addMember(Request $request, Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Samo voditelj projekta može dodavati članove.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        if ($project->members->contains($request->user_id)) {
            return redirect()->route('projects.edit', $project)->with('error', 'Korisnik je već član projekta!');
        }

        $project->members()->attach($request->user_id);

        return redirect()->route('projects.edit', $project)->with('success', 'Član uspješno dodan projektu!');
    }

    public function addTask(Request $request, Project $project)
    {
        if (Auth::id() !== $project->user_id && !$project->members->contains(Auth::id())) {
            abort(403, 'Nemate ovlasti za dodavanje zadataka.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $project->tasks()->create([
            'title' => $request->title,
            'is_completed' => false,
        ]);

        return redirect()->route('projects.edit', $project)->with('success', 'Zadatak uspješno dodan!');
    }

    public function updateTask(Request $request, Project $project, Task $task)
    {
        if (Auth::id() !== $project->user_id && !$project->members->contains(Auth::id())) {
            abort(403, 'Nemate ovlasti za ažuriranje zadataka.');
        }

        if ($task->project_id !== $project->id) {
            abort(403, 'Zadatak ne pripada ovom projektu.');
        }

        $request->validate([
            'is_completed' => 'required|boolean',
        ]);

        $task->update([
            'is_completed' => $request->is_completed,
        ]);

        return redirect()->route('projects.edit', $project)->with('success', 'Zadatak uspješno ažuriran!');
    }
}