<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    @include('partials.styles')
    <title>Pilih Model Kertas</title>

    <style>
        /* === STYLE KHUSUS PILIHAN KERTAS === */
        .paper-grid {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .paper-card {
            width: 170px;
            padding: 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--surface);
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            text-align: center;
        }

        .paper-card:hover {
            border-color: var(--accent);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .paper-card .thumb {
            width: 100%;
            height: 120px;
            overflow: hidden;
            border-radius: 8px;
        }

        .paper-card .thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .paper-card .label {
            font-size: 0.95rem;
            color: var(--muted);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .paper-card input[type=radio] {
            transform: scale(1.1);
        }
    </style>
</head>

<body>

    <h1 class="header">Pilih Model & Warna Kertas</h1>

    <div class="container">
        <form method="post" action="/custom/step/2">
            @csrf

            <!-- PILIH JENIS KERTAS -->
            <div style="margin-bottom:12px">
                <strong>Pilih Jenis Kertas</strong>

                <div class="paper-grid">
                    @foreach($paperTypes as $pt)
                        @php
                            $img = isset($pt['image']) && $pt['image']
                                ? asset($pt['image'])
                                : 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="160" height="80"><rect width="100%" height="100%" fill="%23f5d6d7"/><text x="50%" y="50%" fill="%23000" font-size="12" dominant-baseline="middle" text-anchor="middle">No Image</text></svg>';
                        @endphp

                        <label class="paper-card">
                            <div class="thumb">
                                <img src="{{ $img }}" alt="{{ $pt['label'] }}">
                            </div>
                            <div class="label">
                                <input type="radio" name="paper_type" value="{{ $pt['key'] }}">
                                <span>{{ $pt['label'] }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- PILIH WARNA KERTAS -->
            <div style="margin-bottom:12px">
                <strong>Pilih Warna Kertas</strong>

                <div style="margin-top:8px; display:flex; gap:10px; flex-wrap:wrap;">
                    @foreach($paperColors as $c)
                        <label style="display:flex; align-items:center; gap:8px;">
                            <input type="radio" name="paper_color" value="{{ $c }}">
                            {{ $c }}
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- BUTTON LANJUT -->
            <div>
                <button type="submit">Lanjut ke Langkah 3</button>
            </div>
        </form>

        <p><a href="/custom/reset">Batal & Kembali</a></p>
    </div>

</body>

</html>