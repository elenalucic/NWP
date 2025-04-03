<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';



Route::middleware(['auth'])->group(function () {
Route::resource('projects', ProjectController::class);
Route::post('projects/{project}/add-member', [ProjectController::class, 'addMember'])->name('projects.add-member');
Route::post('/projects/{project}/add-member', [ProjectController::class, 'addMember'])->name('projects.addMember');
Route::get('/profile/projects', [ProfileController::class, 'projects'])->name('profile.projects')->middleware('auth');
Route::post('/projects/{project}/tasks', [ProjectController::class, 'addTask'])->name('projects.addTask')->middleware('auth');
Route::patch('/projects/{project}/tasks/{task}', [ProjectController::class, 'updateTask'])->name('projects.updateTask')->middleware('auth');

});


