<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    @include('partials.styles')
    <style>
        .choices-grid {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .choice-card {
            width: 180px;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px;
            background: #fff;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .choice-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .choice-card img {
            height: 110px;
            width: 100%;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 8px;
        }

        .choice-card input[type="radio"] {
            margin-top: 6px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Pilih Isi Buket — Bunga</h1>

        <form method="post" action="/custom/step/3">
            @csrf

            <p>Pilih bunga yang ingin dimasukkan ke dalam buket Anda. Pilih **satu**.</p>

            @php
                // Mapping nama file → nama tampilan
                $displayNames = [
                    'rose_peony' => 'Rose Peony',
                    'pompom' => 'Pompom',
                    'aster' => 'Aster',
                    'rose_tropis' => 'Rose Tropis',
                    'peony' => 'Peony'
                ];
            @endphp

            <div class="choices-grid">
                @foreach($images as $img)
                    @php
                        $fileName = pathinfo($img, PATHINFO_FILENAME);
                        $displayName = $displayNames[$fileName] ?? $fileName; 
                    @endphp

                    <label class="choice-card">
                        <div style="height:110px;overflow:hidden">
                            <img src="{{ asset('images/step3_bunga/' . $img) }}" alt="{{ $img }}">
                        </div>
                        <div style="margin-top:6px">
                            <input type="radio" name="filling_choice" value="{{ $img }}">
                            <div style="font-size:13px;margin-top:6px">{{ $displayName }}</div>
                        </div>
                    </label>
                @endforeach
            </div>

            <div style="margin-top:18px;display:flex;gap:10px">
                <a class="btn" href="/custom/step/2">Kembali ke Langkah 2</a>
                <button type="submit" class="btn" style="background:var(--accent);color:#fff">
                    Lanjut ke Langkah 4
                </button>
            </div>
        </form>

        <p style="margin-top:12px"><a href="/custom/reset">Batal & Kembali</a></p>
    </div>

</body>

</html>