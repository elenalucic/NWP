<?php
namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TaskController extends Controller
{

 
    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description' => 'required|string',
            'study_type' => 'required|in:professional,undergraduate,graduate',
        ]);

        Task::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'title_en' => $request->title_en,
            'description' => $request->description,
            'study_type' => $request->study_type,
        ]);

        return redirect()->route('dashboard')->with('success', 'Rad je uspješno dodan.');
    }

    public function index()
{
    $tasks = Task::with('user')->get(); // Dohvati sve radove zajedno s nastavnikom koji ih je kreirao
    return view('tasks.index', compact('tasks'));
}


public function apply(Task $task)
{
    $student = Auth::user();


    if ($student->tasks()->where('task_id', $task->id)->exists()) {
        return redirect()->route('tasks.index', app()->getLocale())->with('error', 'Već ste prijavljeni na ovaj rad.');
    }

   
    $student->tasks()->attach($task->id);

    return redirect()->route('tasks.index', app()->getLocale())->with('success', 'Uspješno ste se prijavili na rad.');
}


public function applications()
{
    $tasks = Task::with(['students', 'acceptedStudent'])
                 ->where('user_id', auth::id())
                 ->get();
    return view('tasks.applications', compact('tasks'));
}

public function accept(Task $task, User $student)
{
    // Provjeri je li nastavnik vlasnik rada
    if ($task->user_id !== auth::id()) {
        return redirect()->route('tasks.applications', app()->getLocale())->with('error', 'Nemate ovlasti za ovaj rad.');
    }

    // Provjeri je li student već prihvaćen
    if ($task->accepted_student_id) {
        return redirect()->route('tasks.applications', app()->getLocale())->with('error', 'Već je prihvaćen student za ovaj rad.');
    }

    // Provjeri je li student prijavljen na rad
    if (!$task->students()->where('user_id', $student->id)->exists()) {
        return redirect()->route('tasks.applications', app()->getLocale())->with('error', 'Ovaj student nije prijavljen na rad.');
    }

    // Prihvati studenta
    $task->update(['accepted_student_id' => $student->id]);

    return redirect()->route('tasks.applications', app()->getLocale())->with('success', 'Student je uspješno prihvaćen.');
}

}