<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bayar Pesanan</title>
    @include('partials.styles')
</head>

<body>
    <div
        style="max-width:800px;margin:40px auto;padding:20px;background:var(--surface);border:1px solid var(--border);border-radius:8px;text-align:center">
        <h1>Proses Pembayaran</h1>
        <p>Memuat halaman pembayaran ...</p>
        <p id="msg"></p>
    </div>

    @php
        $clientKey = env('MIDTRANS_CLIENT_KEY');
        $isProd = env('MIDTRANS_IS_PRODUCTION', false);
        $snapJs = $isProd ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
    @endphp

    <script src="{{ $snapJs }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        const token = "{{ $snap_token }}";
        if (!token) {
            document.getElementById('msg').innerText = 'Token pembayaran tidak tersedia.';
        } else {
            // Open Snap payment popup
            window.snap && window.snap.pay(token, {
                onSuccess: function (result) {
                    window.location = '/orders/{{ $order->id }}';
                },
                onPending: function (result) {
                    window.location = '/orders/{{ $order->id }}';
                },
                onError: function (result) {
                    document.getElementById('msg').innerText = 'Terjadi kesalahan saat memproses pembayaran.';
                }
            });
        }
    </script>
</body>

</html>