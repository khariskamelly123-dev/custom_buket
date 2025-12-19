<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Pesan Produk</title>
    @include('partials.styles')
</head>

<body>
    <div class="container">
        <div class="card">
            <h1 class="product-name">Pesan: {{ $bouquet->name }}</h1>
            <div style="display:flex;gap:16px;align-items:flex-start;margin-top:12px">
                <div class="product-media">
                    @if($bouquet->image)
                        <img src="{{ $bouquet->image }}" alt="{{ $bouquet->name }}">
                    @else
                        <div class="thumb">No Image</div>
                    @endif
                </div>

                <div class="product-info">
                    <div>
                        <div class="product-price">Rp {{ number_format($bouquet->price, 0, ',', '.') }}</div>
                        <p style="margin-top:8px">{{ $bouquet->description }}</p>
                    </div>

                    <form method="post" action="/orders" style="margin-top:12px">
                        @csrf
                        <input type="hidden" name="bouquet_id" value="{{ $bouquet->id }}">

                        <label>Nama
                            <input type="text" name="buyer_name" required>
                        </label>

                        <label>Nomor HP
                            <input type="text" name="buyer_phone" required>
                        </label>

                        <label>Alamat (opsional)
                            <input type="text" name="address">
                        </label>

                        <label>Metode Pembayaran
                            <select name="payment_method" required>
                                <option value="COD">COD</option>
                                <option value="Transfer">Transfer</option>
                            </select>
                        </label>

                        <div style="margin-top:12px">
                            <button type="submit" class="btn">Pesan</button>
                            <a href="/buyer" class="btn secondary" style="margin-left:8px">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>