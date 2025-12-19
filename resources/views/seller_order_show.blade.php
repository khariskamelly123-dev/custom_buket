<!doctype html>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    @include('partials.styles')
    <title>Detail Pesanan - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 20px
        }

        .card {
            border: 1px solid #e1e1e1;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 12px;
            background: #fff
        }

        .row {
            display: flex;
            gap: 12px;
            align-items: center
        }

        .thumb {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 4px
        }

        .label {
            color: #666;
            font-size: 13px
        }

        .value {
            font-weight: 600
        }

        .list {
            margin: 8px 0 0 0;
            padding: 0;
            list-style: none
        }

        .list li {
            margin: 6px 0
        }
    </style>
</head>

<body>
    <h1>Detail Pesanan</h1>

    <div class="card">
        <div class="label">Nomor Pesanan</div>
        <div class="value">{{ $order->order_number }}</div>
        <div style="margin-top:8px">
            <span class="label">Status:</span> <strong>{{ $order->status }}</strong>
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <span class="label">Dibuat:</span> {{ $order->created_at }}
        </div>
    </div>

    <div class="card">
        <div class="label">Data Pemesan</div>
        <div class="row" style="margin-top:6px">
            <div>
                <div class="label">Nama</div>
                <div class="value">{{ $order->buyer_name }}</div>
            </div>
            <div>
                <div class="label">Nomor HP</div>
                <div class="value">{{ $order->buyer_phone }}</div>
            </div>
            <div>
                <div class="label">Metode Pembayaran</div>
                <div class="value">{{ $order->payment_method ?? '-' }}</div>
            </div>
        </div>
    </div>

    @php $items = $order->items ?? []; @endphp

    <div class="card">
        <div class="label">Alamat</div>
        <div style="margin-top:6px">
            @if(isset($items['address']) && $items['address'])
                <div class="value">{{ $items['address'] }}</div>
            @elseif(isset($items['identity']['address']) && $items['identity']['address'])
                <div class="value">{{ $items['identity']['address'] }}</div>
            @else
                <div class="value">-</div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="label">Isi Pesanan</div>
        <div style="margin-top:8px">
            <ul class="list">
                @php
                    $typeMap = [
                        'buket_uang' => 'Buket Uang',
                        'buket_bunga' => 'Buket Bunga',
                        'buket_jajan' => 'Buket Jajan'
                    ];
                    $extrasCodeMap = [
                        'e1' => 'Aksesoris 1',
                        'e2' => 'Aksesoris 2',
                        'e3' => 'Aksesoris 3',
                        'e4' => 'Aksesoris 4',
                        'e5' => 'Aksesoris 5',
                    ];
                @endphp

                {{-- Jenis Buket (from custom flow) --}}
                @if(isset($items['bouquet_type']))
                    <li><strong>Jenis Buket:</strong> {{ $typeMap[$items['bouquet_type']] ?? $items['bouquet_type'] }}</li>
                @elseif(isset($items['bouquet']['name']))
                    <li><strong>Jenis Buket:</strong> {{ $items['bouquet']['name'] }}</li>
                @endif
                @if(isset($items['bouquet']))
                    <li>
                        <strong>Bouquet (katalog):</strong> {{ $items['bouquet']['name'] ?? '-' }}
                        @if(isset($items['bouquet']['price'])) - Rp
                        {{ number_format($items['bouquet']['price'], 0, ',', '.') }} @endif
                    </li>
                @endif

                @if(isset($items['paper']))
                    <li><strong>Kertas:</strong> {{ $items['paper']['paper_type'] ?? '-' }} /
                        {{ $items['paper']['paper_color'] ?? '-' }}
                    </li>
                @endif

                @if(isset($items['fillings']))
                    @php $f = $items['fillings']; @endphp
                    @if(isset($f['type']) && $f['type'] === 'uang')
                        <li><strong>Isi (Uang):</strong> Rp {{ number_format($f['denomination'] ?? 0, 0, ',', '.') }} x
                            {{ $f['count'] ?? 0 }} lembar
                        </li>
                    @elseif(isset($f['type']) && $f['type'] === 'bunga')
                        <li>
                            <strong>Isi (Bunga):</strong> {{ $f['choice'] ?? '-' }}
                            @php $img = $f['choice'] ?? null; @endphp
                            @if($img && file_exists(public_path('images/step3_bunga/' . $img)))
                                <div style="margin-top:8px"><img src="{{ asset('images/step3_bunga/' . $img) }}" class="thumb">
                                </div>
                            @endif
                        </li>
                    @elseif(isset($f['type']) && $f['type'] === 'jajan')
                        <li>
                            <strong>Isi (Jajan):</strong> {{ $f['choice'] ?? '-' }}
                            @php $img = $f['choice'] ?? null; @endphp
                            @if($img && file_exists(public_path('images/step3_jajan/' . $img)))
                                <div style="margin-top:8px"><img src="{{ asset('images/step3_jajan/' . $img) }}" class="thumb">
                                </div>
                            @endif
                        </li>
                    @else
                        <li><strong>Isi:</strong> {{ is_array($f) ? json_encode($f, JSON_UNESCAPED_UNICODE) : $f }}</li>
                    @endif
                @endif

                @if(isset($items['extras']) && is_array($items['extras']) && count($items['extras']) > 0)
                    <li><strong>Aksesoris Tambahan:</strong>
                        <div style="display:flex;gap:8px;margin-top:6px;flex-wrap:wrap">
                            @foreach($items['extras'] as $ex)
                                @php
                                    if (isset($extrasCodeMap[$ex])) {
                                        $label = $extrasCodeMap[$ex];
                                    } else {
                                        $name = pathinfo($ex, PATHINFO_FILENAME);
                                        $label = ucwords(str_replace(['_', '-'], ' ', $name));
                                    }
                                @endphp

                                @if(file_exists(public_path('images/step4/' . $ex)))
                                    <div style="text-align:center">
                                        <img src="{{ asset('images/step4/' . $ex) }}" class="thumb">
                                        <div style="font-size:12px">{{ $label }}</div>
                                    </div>
                                @else
                                    <div style="padding:6px;border:1px solid #eee;border-radius:4px">{{ $label }}</div>
                                @endif
                            @endforeach
                        </div>
                    </li>
                @endif

                @if(isset($items['identity']) && is_array($items['identity']))
                    <li><strong>Identitas Pembeli:</strong>
                        <div style="margin-top:6px">
                            <div>Nama: {{ $items['identity']['buyer_name'] ?? '-' }}</div>
                            <div>HP: {{ $items['identity']['buyer_phone'] ?? '-' }}</div>
                        </div>
                    </li>
                @endif

            </ul>
        </div>
    </div>

    <p><a href="/seller/orders">&larr; Kembali ke Daftar Pesanan</a></p>

</body>

</html>
</body>

</html>