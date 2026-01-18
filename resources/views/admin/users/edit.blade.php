@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Role Pengguna: {{ $user->name }}</h2>
    
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Penting untuk method update --}}
        
        <div class="mb-3">
            <label for="role" class="form-label">Pilih Role Baru</label>
            <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                @foreach ($roles as $role)
                    <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>
                        {{ strtoupper($role) }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="btn btn-primary">Simpan Perubahan Role</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection