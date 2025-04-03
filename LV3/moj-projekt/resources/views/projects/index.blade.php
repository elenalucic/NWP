@extends('layouts.app')

@section('title', 'Popis Projekata')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-semibold text-gray-700 mb-6">Popis Projekata</h1>

    @forelse ($projects as $project)
        <div class="mb-4 p-4 border rounded bg-gray-50">
            <h3 class="text-lg font-medium text-gray-800">{{ $project->name }}</h3>
            <p class="text-gray-600">{{ $project->description ?? 'Nema opisa' }}</p>
            <p class="text-gray-500 text-sm">Početak: {{ $project->start_date }} | Kraj: {{ $project->end_date ?? 'Nije završen' }}</p>
            <p class="text-gray-500 text-sm">Voditelj: {{ $project->owner->name }}</p>
            <p class="text-gray-500 text-sm">
                Vaša uloga: 
                @if ($project->user_id === auth()->id())
                    Voditelj
                @else
                    Član
                @endif
            </p>

            <!-- Svi zadatci -->
            <h4 class="text-sm font-semibold text-gray-700 mt-2">Svi zadatci:</h4>
            @forelse ($project->tasks as $task)
                <div class="ml-4 text-gray-600">
                    <span class="{{ $task->is_completed ? 'line-through text-gray-500' : '' }}">{{ $task->title }}</span>
                </div>
            @empty
                <p class="ml-4 text-gray-500 text-sm">Nema zadataka.</p>
            @endforelse

            <!-- Obavljeni zadatci -->
            <h3 class="text-sm font-semibold text-gray-700 mt-2">Obavljeni zadatci</h3>
            @forelse ($project->tasks->where('is_completed', true) as $task)
                <div class="mb-2 flex items-center">
                    <span class="ml-2 text-gray-500">{{ $task->title }}</span>
                </div>
            @empty
                <p class="text-gray-500">Nema obavljenih zadataka.</p>
            @endforelse

            <a href="{{ route('projects.edit', $project) }}" class="text-blue-500 hover:underline">Uredi</a>
        </div>
    @empty
        <p class="text-gray-500">Nemate projekata.</p>
    @endforelse

    <a href="{{ route('projects.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 mt-4 inline-block">
        Kreiraj Novi Projekt
    </a>
</div>
@endsection