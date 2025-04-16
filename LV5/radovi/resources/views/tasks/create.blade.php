@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">{{ __('messages.add_task') }}</h2>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <form action="{{ route('tasks.store', app()->getLocale()) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="title" class="form-label fw-bold">{{ __('messages.title') }}</label>
                                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="title_en" class="form-label fw-bold">{{ __('messages.title_en') }}</label>
                                <input type="text" name="title_en" id="title_en" class="form-control @error('title_en') is-invalid @enderror" value="{{ old('title_en') }}">
                                @error('title_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="description" class="form-label fw-bold">{{ __('messages.description') }}</label>
                                <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="study_type" class="form-label fw-bold">{{ __('messages.study_type') }}</label>
                                <select name="study_type" id="study_type" class="form-select @error('study_type') is-invalid @enderror">
                                    <option value="professional" {{ old('study_type') == 'professional' ? 'selected' : '' }}>{{ __('messages.professional') }}</option>
                                    <option value="undergraduate" {{ old('study_type') == 'undergraduate' ? 'selected' : '' }}>{{ __('messages.undergraduate') }}</option>
                                    <option value="graduate" {{ old('study_type') == 'graduate' ? 'selected' : '' }}>{{ __('messages.graduate') }}</option>
                                </select>
                                @error('study_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4 py-2">{{ __('messages.submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection