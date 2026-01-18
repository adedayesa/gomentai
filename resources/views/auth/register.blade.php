<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Go Mentai</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0; /* Tambahan padding agar tidak mentok atas bawah di HP */
        }
        .auth-card {
            background: white;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px; /* Sedikit lebih lebar dari login karena inputnya banyak */
        }
        /* Style Input dengan Icon */
        .input-icon-group {
            position: relative;
            margin-bottom: 1.2rem;
        }
        .input-icon-group i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            z-index: 10;
        }
        .rounded-pill-input {
            border-radius: 50px;
            padding-left: 50px;
            padding-top: 12px;
            padding-bottom: 12px;
            background-color: #f1f1f1;
            border: 1px solid #eee;
        }
        .rounded-pill-input:focus {
            background-color: #fff;
            border-color: #FF6F00;
            box-shadow: 0 0 0 0.25rem rgba(255, 111, 0, 0.25);
        }
        /* Tombol Utama */
        .action-btn {
            background-color: #FF6F00;
            color: white;
            width: 100%;
            border-radius: 50px;
            padding: 12px;
            font-weight: bold;
            border: none;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .action-btn:hover {
            background-color: #e65100;
            color: white;
        }
        a { text-decoration: none; }
    </style>
</head>
<body>

    <div class="auth-card">
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo2.jpeg') }}" 
                alt="Logo Go Mentai" 
                class="mb-2"
                style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">
            <h2 class="mt-2" style="font-weight: bold; color: #333;">GO MENTAI</h2>
            <p class="small text-muted">Buat Akun Baru</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="input-icon-group">
                <i class="fas fa-user"></i>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus 
                       class="form-control rounded-pill-input @error('name') is-invalid @enderror" 
                       placeholder="Nama Lengkap">
                @error('name')
                    <div class="text-danger small mt-1 ps-3">{{ $message }}</div>
                @enderror
            </div>

            <div class="input-icon-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" value="{{ old('email') }}" required 
                       class="form-control rounded-pill-input @error('email') is-invalid @enderror" 
                       placeholder="Alamat Email">
                @error('email')
                    <div class="text-danger small mt-1 ps-3">{{ $message }}</div>
                @enderror
            </div>

            <div class="input-icon-group">
                <i class="fas fa-phone"></i>
                <input type="text" name="phone" value="{{ old('phone') }}" required
                    class="form-control rounded-pill-input @error('phone') is-invalid @enderror"
                    placeholder="Nomor Telepon">
                @error('phone')
                    <div class="text-danger small mt-1 ps-3">{{ $message }}</div>
                @enderror
            </div>

            <div class="input-icon-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" required autocomplete="new-password"
                       class="form-control rounded-pill-input @error('password') is-invalid @enderror" 
                       placeholder="Password">
                @error('password')
                    <div class="text-danger small mt-1 ps-3">{{ $message }}</div>
                @enderror
            </div>

            <div class="input-icon-group">
                <i class="fas fa-key"></i>
                <input type="password" name="password_confirmation" required 
                       class="form-control rounded-pill-input" 
                       placeholder="Konfirmasi Password">
            </div>

            <button type="submit" class="btn action-btn">
                DAFTAR SEKARANG
            </button>
        </form>

        <div class="text-center mt-4 small">
            Sudah punya akun? <a href="{{ route('login') }}" style="color: #FF6F00; font-weight: bold;">Login disini</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>