@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">{{ __('Prijave na vaše radove') }}</h2>
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
                            <p class="text-muted">{{ __('Nemate radova.') }}</p>
                        @else
                            @foreach ($tasks as $task)
                                <div class="card mb-3">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="mb-0">{{ app()->getLocale() == 'hr' ? $task->title : $task->title_en }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>{{ __('Tip studija') }}:</strong> {{ __('messages.' . $task->study_type) }}</p>
                                        @if ($task->acceptedStudent)
                                            <p class="text-success"><strong>{{ __('Prihvaćeni student') }}:</strong> {{ $task->acceptedStudent->name }}</p>
                                        @else
                                            <p class="text-muted">{{ __('Nema prihvaćenog studenta.') }}</p>
                                        @endif
                                        <h6>{{ __('Prijavljeni studenti') }}:</h6>
                                        @if ($task->students->isEmpty())
                                            <p class="text-muted">{{ __('Nema prijavljenih studenata.') }}</p>
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>{{ __('Ime') }}</th>
                                                            <th>{{ __('Email') }}</th>
                                                            <th>{{ __('Akcija') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($task->students as $student)
                                                            <tr>
                                                                <td>{{ $student->name }}</td>
                                                                <td>{{ $student->email }}</td>
                                                                <td>
                                                                    @if (!$task->accepted_student_id)
                                                                        <form action="{{ route('tasks.accept', [$task, $student, 'locale' => app()->getLocale()]) }}" method="POST">
                                                                            @csrf
                                                                            <button type="submit" class="btn btn-success btn-sm">{{ __('Prihvati') }}</button>
                                                                        </form>
                                                                    @else
                                                                        <span class="text-muted">{{ __('Rad je već dodijeljen.') }}</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection