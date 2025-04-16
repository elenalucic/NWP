<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TaskController;

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

    Route::group(['middleware' => ['auth', 'role:admin']], function () {
        Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.users');
        Route::put('/admin/users/{user}/role', [AdminController::class, 'updateRole'])->name('admin.updateRole');
    });

    Route::group(['middleware' => ['auth', 'role:nastavnik']], function () {
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    });

    Route::group(['middleware' => ['auth', 'role:student']], function () {
        Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/tasks/{task}/apply', [TaskController::class, 'apply'])->name('tasks.apply');
    });

    Route::group(['middleware' => ['auth', 'role:nastavnik']], function () {
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    });

    Route::group(['middleware' => ['auth', 'role:student']], function () {
        Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/tasks/{task}/apply', [TaskController::class, 'apply'])->name('tasks.apply');
    });

    Route::group(['middleware' => ['auth', 'role:nastavnik']], function () {
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/tasks/applications', [TaskController::class, 'applications'])->name('tasks.applications');
        Route::post('/tasks/{task}/accept/{student}', [TaskController::class, 'accept'])->name('tasks.accept');
    });

require __DIR__.'/auth.php';
