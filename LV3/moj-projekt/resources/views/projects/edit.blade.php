@extends('layouts.app')

@section('title', 'Uredi Projekt')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Uredi Projekt: {{ $project->name }}</h2>

    @if (auth()->id() === $project->user_id || $project->members->contains(auth()->id()))
        @if (auth()->id() === $project->user_id)
            <form action="{{ route('projects.update', $project) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <label class="block text-gray-700">Naziv projekta</label>
                    <input type="text" name="name" value="{{ $project->name }}" class="w-full p-2 border rounded" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Opis</label>
                    <textarea name="description" class="w-full p-2 border rounded">{{ $project->description }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Cijena</label>
                    <input type="number" name="price" value="{{ $project->price }}" step="0.01" class="w-full p-2 border rounded">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Datum početka</label>
                    <input type="date" name="start_date" value="{{ $project->start_date }}" class="w-full p-2 border rounded" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Datum završetka</label>
                    <input type="date" name="end_date" value="{{ $project->end_date }}" class="w-full p-2 border rounded">
                </div>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Spremi Izmjene
                </button>
            </form>
        @endif

        <h3 class="text-lg font-semibold text-gray-700 mt-6 mb-2">Dodaj novi zadatak</h3>
        <form action="{{ route('projects.addTask', $project) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700">Naziv zadatka</label>
                <input type="text" name="title" class="w-full p-2 border rounded" required>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                Dodaj zadatak
            </button>
        </form>

        <h3 class="text-lg font-semibold text-gray-700 mt-6 mb-2">Svi zadatci</h3>
        @forelse ($project->tasks as $task)
            <div class="mb-2 flex items-center">
                <form action="{{ route('projects.updateTask', [$project, $task]) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input 
                        type="checkbox" 
                        name="is_completed" 
                        value="1" 
                        {{ $task->is_completed ? 'checked' : '' }} 
                        onchange="this.form.submit()"
                    >
                    <span class="ml-2 {{ $task->is_completed ? 'line-through text-gray-500' : 'text-gray-700' }}">{{ $task->title }}</span>
                </form>
            </div>
        @empty
            <p class="text-gray-500">Nema zadataka za ovaj projekt.</p>
        @endforelse

        <!-- Obavljeni zadatci -->
        <h3 class="text-lg font-semibold text-gray-700 mt-6 mb-2">Obavljeni zadatci</h3>
        @forelse ($project->tasks->where('is_completed', true) as $task)
            <div class="mb-2 flex items-center">
                <span class="ml-2 text-gray-500">{{ $task->title }}</span>
            </div>
        @empty
            <p class="text-gray-500">Nema obavljenih zadataka.</p>
        @endforelse

        @if (auth()->id() === $project->user_id)
            <h2 class="text-xl font-semibold text-gray-700 mt-6 mb-4">Uredi članove projektnog tima</h2>
            <form action="{{ route('projects.updateMembers', $project) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <label class="block text-gray-700">Dodaj novog člana</label>
                    <select name="new_member" class="w-full p-2 border rounded">
                        <option value="">-- Odaberi korisnika --</option>
                        @foreach (\App\Models\User::all() as $user)
                            @if ($user->id !== $project->user_id && !$project->members->contains($user->id))
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <h3 class="text-lg font-semibold text-gray-700 mb-2">Trenutni članovi tima:</h3>
                <ul class="list-disc pl-5 mb-4">
                    @forelse ($project->members as $member)
                        <li class="text-gray-700 flex items-center">
                            {{ $member->name }} ({{ $member->email }})
                            <input type="checkbox" name="remove_members[]" value="{{ $member->id }}" class="ml-2">
                            <label class="ml-1 text-sm text-gray-600">Ukloni</label>
                        </li>
                    @empty
                        <li class="text-gray-500">Nema dodanih članova.</li>
                    @endforelse
                </ul>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Spremi
                </button>
            </form>
        @endif
    @else
        <p class="text-red-500">Nemate ovlasti za uređivanje ovog projekta.</p>
    @endif
</div>
@endsection