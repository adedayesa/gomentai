@extends('layouts.app') 

@section('title', 'Detail Produk')

@section('content')
    <h1 class="mb-4">Detail Kustomisasi</h1>

    <div class="card shadow-sm">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="{{ asset($product->image ?? 'images/default.jpg') }}" class="img-fluid rounded-start" alt="{{ $product->name }}">
            </div>
            
            <div class="col-md-8">
                <div class="card-body">
                    
                    <form action="{{ route('cart.add', $product) }}" method="POST">
                        @csrf 

                        <h3 class="card-title">{{ $product->name }}</h3>
                        <p class="text-danger fw-bold fs-5">Rp {{ number_format($product->base_price, 0, ',', '.') }}</p>
                        
                        @foreach($product->options as $option)
                            <h6 class="mt-3">{{ $option->name }} *</h6>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($option->values as $value)
                                    <div class="form-check">
                                        <input 
                                            class="form-check-input" 
                                            type="radio" 
                                            name="options[{{ $option->id }}]" 
                                            value="{{ $value->id }}" 
                                            id="option_{{ $value->id }}" 
                                            required
                                        >
                                        <label class="form-check-label" for="option_{{ $value->id }}">
                                            {{ $value->name }} 
                                            @if($value->price_modifier > 0)
                                                (+Rp {{ number_format($value->price_modifier, 0, ',', '.') }})
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <hr>
                        
                        <div class="d-flex align-items-center mb-3">
                            <label for="quantity" class="form-label me-3">Kuantitas:</label>
                            <input type="number" name="quantity" value="1" min="1" class="form-control" style="width: 80px;" required>
                        </div>

                        <button type="submit" class="btn btn-danger btn-lg w-100">
                            Tambahkan ke Keranjang
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection