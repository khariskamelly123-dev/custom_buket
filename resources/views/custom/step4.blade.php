<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Pilih Aksesoris (Step 4)</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
            color: #000000;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 28px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        /* --- membuat gambar hanya 1 baris --- */
        .accessories-row {
            display: flex;
            gap: 18px;
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 10px;
            scrollbar-width: thin;
        }

        .accessories-row::-webkit-scrollbar {
            height: 8px;
        }

        .accessories-row::-webkit-scrollbar-thumb {
            background: #e9aeab;
            border-radius: 10px;
        }

        .item-card {
            min-width: 180px;
            max-width: 180px;
            background: #ffffff;
            border: 2px solid #e9aeab;
            border-radius: 14px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: 0.25s ease;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.07);
        }

        .item-card:hover {
            transform: translateY(-4px);
            background: #e9aeab30;
        }

        .item-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 8px;
        }

        .item-card input[type="checkbox"] {
            transform: scale(1.2);
            accent-color: #e9aeab;
            cursor: pointer;
            margin-right: 6px;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #e9aeab;
            color: #000000;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: 0.2s ease;
        }

        button:hover {
            background: #d48c87;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #000000;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Pilih Aksesoris Tambahan</h1>

        <form method="post" action="/custom/step/4">
            @csrf

            <strong>Pilih satu atau beberapa aksesoris:</strong>

            <div class="accessories-row">
                @if(!empty($images))
                    @foreach($images as $img)
                        <label class="item-card">
                            <img src="{{ asset('images/step4/' . $img) }}" alt="{{ $img }}">

                            <div>
                                <input type="checkbox" name="extras[]" value="{{ $img }}">
                                {{ pathinfo($img, PATHINFO_FILENAME) }}
                            </div>
                        </label>
                    @endforeach
                @else
                    <p>Tidak ada gambar aksesoris tersedia.</p>
                @endif
            </div>

            <button type="submit" style="margin-top:25px;">Lanjut ke Langkah 5</button>
        </form>

        <div class="back-link">
            <a href="/custom/reset">Batal & Kembali</a>
        </div>
    </div>

</body>

</html>