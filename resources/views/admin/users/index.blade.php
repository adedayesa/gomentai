@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm mb-2 shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Dashboard Admin
            </a>
            <h1 class="h3 fw-bold text-dark mb-0">Daftar Pesanan Masuk</h1>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone ?? '-' }}</td>
                <td>
                    <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                        {{ strtoupper($user->role) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit Role</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection