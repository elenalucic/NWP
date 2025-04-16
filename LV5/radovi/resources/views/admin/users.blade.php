@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">{{ __('Upravljanje ulogama korisnika') }}</h2>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Ime') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Uloga') }}</th>
                                        <th>{{ __('Akcija') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($korisnici as $korisnik)
                                        <tr>
                                            <td>{{ $korisnik->name }}</td>
                                            <td>{{ $korisnik->email }}</td>
                                            <td>{{ $korisnik->role }}</td>
                                            <td>
                                                <form action="{{ route('admin.updateRole', [$korisnik, 'locale' => app()->getLocale()]) }}" method="POST" class="d-flex align-items-center gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="role" class="form-select w-auto">
                                                        <option value="admin" {{ $korisnik->role == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                                                        <option value="nastavnik" {{ $korisnik->role == 'nastavnik' ? 'selected' : '' }}>{{ __('Nastavnik') }}</option>
                                                        <option value="student" {{ $korisnik->role == 'student' ? 'selected' : '' }}>{{ __('Student') }}</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-primary btn-sm">{{ __('Ažuriraj') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection