@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">{{ __('Popis radova') }}</h2>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if ($tasks->isEmpty())
                            <p class="text-muted">{{ __('Trenutno nema dostupnih radova.') }}</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Naslov') }}</th>
                                            <th>{{ __('Nastavnik') }}</th>
                                            <th>{{ __('Tip studija') }}</th>
                                            <th>{{ __('Akcija') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tasks as $task)
                                            <tr>
                                                <td>{{ app()->getLocale() == 'hr' ? $task->title : $task->title_en }}</td>
                                                <td>{{ $task->user->name }}</td>
                                                <td>{{ __( 'messages.' . $task->study_type) }}</td>
                                                <td>
                                                    <form action="{{ route('tasks.apply', [$task, 'locale' => app()->getLocale()]) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm">{{ __('Prijavi se') }}</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection