<!-- resources/views/projects/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-semibold text-gray-700 mb-4">Kreiraj novi projekt</h1>

    <form action="{{ route('projects.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700">Naziv projekta:</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full p-2 border rounded" required>
            @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Opis:</label>
            <textarea name="description" class="w-full p-2 border rounded">{{ old('description') }}</textarea>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Cijena:</label>
            <input type="number" name="price" value="{{ old('price') }}" step="0.01" class="w-full p-2 border rounded">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Obavljeni poslovi:</label>
            <textarea name="completed_tasks" class="w-full p-2 border rounded">{{ old('completed_tasks') }}</textarea>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Datum početka:</label>
            <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full p-2 border rounded" required>
            @error('start_date')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Datum završetka:</label>
            <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full p-2 border rounded">
            @error('end_date')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

 
        <div class="mb-4">
            <label class="block text-gray-700">Odaberi članove tima (drži Ctrl za višestruki odabir):</label>
            <select name="members[]" multiple class="w-full p-2 border rounded">
                @foreach (\App\Models\User::where('id', '!=', auth()->id())->get() as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
            Kreiraj projekt
        </button>
    </form>
</div>
@endsection