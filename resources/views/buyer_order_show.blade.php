<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Konfirmasi Pesanan</title>
    @include('partials.styles')
</head>

<body>
    <div style="
        max-width:800px;
        margin:40px auto;
        padding:20px;
        background:var(--surface);
        border:1px solid var(--border);
        border-radius:8px
    ">
        <h1>Pesanan Anda Telah Dibuat</h1>

        {{-- pesan sukses --}}
        @if(session('success'))
            <div style="color:green; margin-bottom:10px">
                {{ session('success') }}
            </div>
        @endif

        {{-- info order --}}
        <p><strong>Nomor Pesanan:</strong> {{ $order->order_number }}</p>
        <p><strong>Nama:</strong> {{ $order->buyer_name }}</p>
        <p><strong>Telepon:</strong> {{ $order->buyer_phone }}</p>
        <p><strong>Status:</strong> {{ $order->status }}</p>

        <hr style="margin:16px 0">

        <h3>Isi Pesanan</h3>

        @php
            $items = $order->items ?? [];
        @endphp

        <div style="background:#fff;padding:12px;border-radius:6px">

            {{-- bouquet katalog --}}
            @if(isset($items['bouquet']))
                <div>
                    <strong>Bouquet (Katalog):</strong>
                    {{ $items['bouquet']['name'] ?? '-' }}
                    @if(isset($items['bouquet']['price']))
                        - Rp {{ number_format($items['bouquet']['price'], 0, ',', '.') }}
                    @endif
                </div>
            @endif

            {{-- bouquet custom --}}
            @if(isset($items['bouquet_type']))
                <div>
                    <strong>Jenis Buket (Custom):</strong>
                    {{ $items['bouquet_type'] }}
                </div>
            @endif

            {{-- kertas --}}
            @if(isset($items['paper']))
                <div>
                    <strong>Kertas:</strong>
                    {{ $items['paper']['paper_type'] ?? '-' }}
                    /
                    {{ $items['paper']['paper_color'] ?? '-' }}
                </div>
            @endif

            {{-- isi --}}
            @if(isset($items['fillings']))
                @php $f = $items['fillings']; @endphp

                @if(($f['type'] ?? '') === 'uang')
                    <div>
                        <strong>Isi (Uang):</strong>
                        Rp {{ number_format($f['denomination'] ?? 0, 0, ',', '.') }}
                        x {{ $f['count'] ?? 0 }} lembar
                    </div>
                @elseif(($f['type'] ?? '') === 'bunga')
                    <div>
                        <strong>Isi (Bunga):</strong>
                        {{ $f['choice'] ?? '-' }}
                    </div>
                @elseif(($f['type'] ?? '') === 'jajan')
                    <div>
                        <strong>Isi (Jajan):</strong>
                        {{ $f['choice'] ?? '-' }}
                    </div>
                @else
                    <div>
                        <strong>Isi:</strong>
                        {{ is_array($f) ? json_encode($f, JSON_UNESCAPED_UNICODE) : $f }}
                    </div>
                @endif
            @endif

            {{-- aksesoris --}}
            @if(!empty($items['extras']) && is_array($items['extras']))
                <div>
                    <strong>Aksesoris Tambahan:</strong>
                    {{ implode(', ', $items['extras']) }}
                </div>
            @endif

            {{-- alamat --}}
            @if(!empty($items['address']))
                <div>
                    <strong>Alamat:</strong>
                    {{ $items['address'] }}
                </div>
            @elseif(!empty($items['identity']['address']))
                <div>
                    <strong>Alamat:</strong>
                    {{ $items['identity']['address'] }}
                </div>
            @endif

            {{-- identitas --}}
            @if(isset($items['identity']))
                <div style="margin-top:8px">
                    <strong>Identitas Pembeli:</strong>
                    <div>Nama: {{ $items['identity']['buyer_name'] ?? '-' }}</div>
                    <div>HP: {{ $items['identity']['buyer_phone'] ?? '-' }}</div>
                </div>
            @endif
        </div>

        <hr style="margin:16px 0">

        {{-- tombol kembali --}}
        <a href="/buyer/catalog" class="btn">Kembali ke Katalog</a>

        {{-- pembayaran --}}
        @if(($order->payment_status ?? '') !== 'paid')
            <form id="pay-form-{{ $order->id }}" action="{{ route('orders.pay', $order->id) }}" method="POST" style="margin-top:12px">
                @csrf
                <button type="button" class="btn" id="pay-btn-{{ $order->id }}">
                    Bayar Sekarang
                </button>
            </form>
        @else
            <p style="margin-top:12px;color:green">
                Pembayaran: Lunas
            </p>
        @endif
    </div>
</body>

</html>

<!-- No JS fallback: use normal form submit so browser follows redirects and middleware correctly -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('pay-form-{{ $order->id }}');
        if (!form) return;
        var btn = document.getElementById('pay-btn-{{ $order->id }}') || form.querySelector('button');
        if (!btn) return;

        // create overlay feedback
        var overlay = document.createElement('div');
        overlay.style.position = 'fixed';
        overlay.style.left = 0;
        overlay.style.top = 0;
        overlay.style.right = 0;
        overlay.style.bottom = 0;
        overlay.style.background = 'rgba(255,255,255,0.8)';
        overlay.style.display = 'none';
        overlay.style.alignItems = 'center';
        overlay.style.justifyContent = 'center';
        overlay.style.zIndex = 9999;
        overlay.innerHTML = '<div style="background:#fff;padding:18px;border-radius:8px;border:1px solid #eee;font-weight:700;color:#333">Memproses pembayaran...</div>';
        document.body.appendChild(overlay);

        function showProcessing() {
            overlay.style.display = 'flex';
            btn.disabled = true;
            btn.style.opacity = '0.7';
        }

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            showProcessing();

            // Try normal form submission first
            try {
                form.submit();
            } catch (err) {
                console.error('form.submit failed', err);
            }

            // If navigation doesn't happen within 800ms, fallback to fetch POST
            var initialHref = window.location.href;
            setTimeout(function () {
                if (window.location.href === initialHref) {
                    // fallback using fetch
                    var action = form.getAttribute('action') || window.location.href;
                    var method = (form.getAttribute('method') || 'GET').toUpperCase();
                    var fd = new FormData(form);

                    fetch(action, {
                        method: method,
                        body: fd,
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        redirect: 'follow'
                    }).then(function (res) {
                        if (res.redirected) {
                            window.location = res.url;
                            return;
                        }
                        return res.text().then(function (html) {
                            // replace document with response (works for same-origin views)
                            document.open();
                            document.write(html);
                            document.close();
                        });
                    }).catch(function (err) {
                        console.error('Fetch fallback failed', err);
                        overlay.style.display = 'none';
                        btn.disabled = false;
                        btn.style.opacity = '';
                        alert('Gagal menghubungi server. Cek console untuk detail.');
                    });
                }
            }, 800);
        });
    });
</script>