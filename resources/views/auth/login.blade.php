<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Go Mentai</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa; /* Warna background halaman abu muda */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        /* Style khusus untuk input dengan icon di dalam */
        .input-icon-group {
            position: relative;
            margin-bottom: 1.5rem;
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
            padding-left: 50px; /* Memberi ruang agar teks tidak menabrak icon */
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
        /* Tombol Login Oranye */
        .action-btn {
            background-color: #FF6F00;
            color: white;
            width: 100%;
            border-radius: 50px;
            padding: 12px;
            font-weight: bold;
            border: none;
            transition: all 0.3s;
        }
        .action-btn:hover {
            background-color: #e65100;
            color: white;
        }
        /* Link warna oranye */
        a {
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo2.jpeg') }}" 
                alt="Logo Go Mentai" 
                class="mb-2"
                style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">
            <h2 class="mt-2" style="font-weight: bold;">GO MENTAI</h2>
        </div>

        <div class="d-flex justify-content-center gap-3 mb-4">
            <a href="#" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fab fa-google"></i></a>
            <a href="#" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fab fa-facebook-f"></i></a>
        </div>
        
        <p class="text-center small text-muted">atau</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-icon-group">
                <i class="fas fa-user"></i>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control rounded-pill-input" placeholder="Username / Email">
            </div>

            <div class="input-icon-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" required autocomplete="current-password" class="form-control rounded-pill-input" placeholder="Password">
            </div>

            <div class="d-flex justify-content-between small mb-4">
                <label>
                    <input type="checkbox" name="remember">
                    Ingat Saya
                </label>
                <a href="{{ route('password.request') }}" class="text-muted">Lupa Password?</a>
            </div>

            <button type="submit" class="btn action-btn">
                LOGIN
            </button>
        </form>

        <div class="text-center mt-4 small">
            Belum punya akun? <a href="{{ route('register') }}" style="color: #FF6F00; font-weight: bold;">Daftar Sekarang</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>