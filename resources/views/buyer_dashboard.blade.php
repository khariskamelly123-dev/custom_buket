<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    @include('partials.styles')
    <title>Dashboard Pembeli</title>

    <style>
        /* GRID = 3 item per baris */
        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
            margin-top: 20px;
        }

        /* CARD KECIL */
        .catalog-item {
            display: flex;
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 14px;
            gap: 14px;
            min-height: 160px;
        }

        /* FOTO KIRI */
        .img-box {
            width: 110px;
            height: 140px;
            background: #000;
            border-radius: 8px;
            overflow: hidden;
        }

        .img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .no-img {
            width: 100%;
            height: 100%;
            background: #eee;
            color: #999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* INFO KANAN */
        .info-box {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .info-box h3 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .price {
            margin: 6px 0;
            font-size: 16px;
            font-weight: bold;
        }

        /* TOMBOL PESAN */
        .btn-pesan {
            background: #e9aeab;
            color: white;
            padding: 6px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            width: fit-content;
            font-weight: 600;
        }

        .btn-pesan:hover {
            background: #333;
        }

        /* BUTTON CUSTOM */
        .btn-custom {
            display: inline-block;
            padding: 8px 14px;
            background: #e9aeab;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
        }
    </style>
</head>

<body>
    <div style="max-width:1100px;margin:24px auto;position:relative">
        <div style="display:flex;align-items:center;justify-content:space-between">
            <h1>Selamat datang di toko buket</h1>
            <a class="btn-custom" href="/custom/step/1">Pesan Custom Buket</a>
        </div>

        <p>Berikut katalog buket yang sudah jadi:</p>

        <!-- GRID KATALOG -->
        <div class="catalog-grid">
            @foreach($bouquets as $b)
                <div class="catalog-item">

                    <!-- FOTO -->
                    <div class="img-box">
                        @if($b->image)
                            <img src="{{ $b->image }}" alt="{{ $b->name }}">
                        @else
                            <div class="no-img">No Image</div>
                        @endif
                    </div>

                    <!-- INFO -->
                    <div class="info-box">
                        <div>
                            <h3>{{ $b->name }}</h3>
                            <p class="price">Rp {{ number_format($b->price, 0, ',', '.') }}</p>
                        </div>

                        @php
                            $waNumber = '6283104866204';
                            $waText = "Halo, saya ingin memesan buket '" . $b->name . "' (ID: " . $b->id . ") - Rp " . number_format($b->price, 0, ',', '.');
                        @endphp
                        <a class="btn-pesan" href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode($waText) }}"
                            target="_blank" rel="noopener noreferrer">pesan</a>
                    </div>
                </div>
            @endforeach
        </div>

        <p style="margin-top:18px"><a href="/">Kembali</a></p>
    </div>
</body>

</html>