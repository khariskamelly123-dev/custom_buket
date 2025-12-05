<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    @include('partials.styles')
    <title>Buat Pesanan - Pembeli</title>
</head>

<body>
    <h1>Buat Pesanan (Pembeli)</h1>
    @if(session('success'))
        <div style="color:green">{{ session('success') }}</div>
    @endif

    <form method="post" action="/orders">
        @csrf
        <div>
            <label>Nama: <input name="buyer_name" required></label>
        </div>
        <div>
            <label>Nomor Telepon: <input name="buyer_phone" required></label>
        </div>
        <div>
            <label>Metode Pembayaran: <input name="payment_method"></label>
        </div>
        <div>
            <label>Items (JSON): <textarea name="items"></textarea></label>
        </div>
        <div>
            <button type="submit">Buat Pesanan</button>
        </div>
    </form>

    <p><a href="/">Kembali</a></p>
</body>

</html>