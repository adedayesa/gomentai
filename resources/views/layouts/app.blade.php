<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Go Mentai | @yield('title')</title> 
    
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .nav-link.location-link {
            transition: background-color 0.2s;
        }
        .nav-link.location-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.3rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">GO MENTAI</a>

            @if(session()->has('user_location'))
                <a id="location-trigger-btn" href="#" 
                   class="nav-link location-link text-white me-3 d-none d-lg-block" 
                   data-bs-toggle="modal" data-bs-target="#locationModal"
                   style="font-size: 0.9rem; padding: 0.5rem 0.5rem;">
                    <i class="bi bi-geo-alt-fill text-warning me-1"></i> 
                    Mengantar ke: **{{ session('user_location')['address'] ?? 'Lokasi Terpilih' }}**
                </a>
            @else
                <a id="location-trigger-btn" href="#" 
                   class="nav-link location-link text-white me-3 d-none d-lg-block" 
                   data-bs-toggle="modal" data-bs-target="#locationModal"
                   style="font-size: 0.9rem; padding: 0.5rem 0.5rem;">
                    <i class="bi bi-geo-alt-fill text-warning me-1"></i> 
                    **Tentukan Lokasi Anda**
                </a>
            @endif

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @guest 
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-light" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item me-2">
                            <a class="btn btn-warning" href="{{ route('register') }}">Register</a>
                        </li>
                    @else 
                        <li class="nav-item me-2">
                            <span class="navbar-text me-2 text-white">Halo, {{ Auth::user()->name }}</span>
                        </li>
                        <li class="nav-item me-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-light">Logout</button>
                            </form>
                        </li>
                    @endguest
                    
                    <li class="nav-item">
                        <a class="btn btn-warning" href="{{ route('cart.index') }}">
                            Keranjang 
                            <span class="badge text-bg-dark">
                                {{ count(session('cart', [])) }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content') 
    </div>

    <div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="locationModalLabel">Pilih Lokasi Layanan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Pilih bagaimana Anda ingin menentukan area layanan:</p>
            <button id="btn-use-gps" class="btn btn-danger w-100 mb-3">
                <i class="bi bi-geo-alt-fill me-2"></i> Gunakan Lokasi Saya Saat Ini (GPS)
            </button>
            <hr>
            <label for="manual_address" class="form-label">Atau Cari Alamat Manual</label>
            <input type="text" id="manual_address" class="form-control" placeholder="Contoh: Kantor Pos Cimenyan">
            <small class="text-muted">Ini untuk menentukan toko terdekat dari posisi Anda.</small>
            <button id="btn-search-manual" class="btn btn-outline-secondary w-100 mt-2" disabled>Cari Lokasi</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        const btnUseGps = document.getElementById('btn-use-gps');
        const inputManualAddress = document.getElementById('manual_address');
        const btnSearchManual = document.getElementById('btn-search-manual');

        // Event Listener untuk input manual
        if (inputManualAddress && btnSearchManual) {
            btnSearchManual.disabled = true;
            inputManualAddress.addEventListener('input', function() {
                btnSearchManual.disabled = (this.value.trim().length <= 3);
            });
            btnSearchManual.addEventListener('click', handleManualSearch);
        }

        // Event Listener untuk tombol GPS
        if (btnUseGps) {
            btnUseGps.addEventListener('click', function(event) {
                event.preventDefault();
                const originalHtml = btnUseGps.innerHTML;
                btnUseGps.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mencari...';
                btnUseGps.disabled = true;

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            processReverseGeocoding(position.coords.latitude, position.coords.longitude);
                        },
                        function(error) {
                            alert('Gagal mendapatkan lokasi GPS. Pastikan izin aktif.');
                            btnUseGps.innerHTML = originalHtml;
                            btnUseGps.disabled = false;
                        },
                        { enableHighAccuracy: true, timeout: 5000 }
                    );
                } else {
                    alert("Browser tidak mendukung GPS.");
                    btnUseGps.innerHTML = originalHtml;
                    btnUseGps.disabled = false;
                }
            });
        }
        
        // Fungsi Mencari Alamat (Forward Geocoding)
        function handleManualSearch(event) {
            event.preventDefault();
            const address = inputManualAddress.value.trim();
            const originalHtml = btnSearchManual.innerHTML;
            btnSearchManual.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mencari...';
            btnSearchManual.disabled = true;

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`, {
                headers: { 'User-Agent': 'GoMentaiApp' }
            })
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    // Ambil nama singkat dari display_name
                    const shortName = data[0].display_name.split(',')[0];
                    sendLocationToServer(data[0].lat, data[0].lon, shortName);
                } else {
                    alert('Alamat tidak ditemukan.');
                    btnSearchManual.innerHTML = originalHtml;
                    btnSearchManual.disabled = false;
                }
            })
            .catch(() => {
                alert('Gagal menghubungi server peta.');
                btnSearchManual.innerHTML = originalHtml;
                btnSearchManual.disabled = false;
            });
        }

        // Fungsi Mengubah Koordinat jadi Nama Alamat (Reverse Geocoding)
        function processReverseGeocoding(lat, lon) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`, {
                headers: { 'User-Agent': 'GoMentaiApp' }
            })
            .then(response => response.json())
            .then(data => {
                const name = data.address.road || data.address.suburb || data.address.city || "Lokasi Terpilih";
                sendLocationToServer(lat, lon, name);
            })
            .catch(() => {
                sendLocationToServer(lat, lon, "Lokasi GPS");
            });
        }

        // Fungsi Mengirim data ke Laravel
        function sendLocationToServer(lat, lon, addressName) {
            fetch('{{ route("set.location") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ lat, lon, address: addressName })
            })
            .then(() => window.location.reload())
            .catch(() => alert('Gagal menyimpan ke server.'));
        }
    </script>
</body>
</html>