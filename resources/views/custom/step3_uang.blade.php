<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Pilih Isi Buket Uang</title>

    <style>
        :root {
            --bg: #ffffff;
            --accent: #e9abae;
            --text: #000000;
            --border: #e4c4c6;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h1 {
            margin-bottom: 16px;
            font-size: 1.6rem;
            color: var(--text);
        }

        label strong {
            color: var(--text);
        }

        /* Radio options container */
        .option-group {
            margin-top: 8px;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        /* Wrapper tiap item */
        .option-item {
            padding: 8px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .option-item:hover {
            background: #f9e6e7;
            border-color: var(--accent);
        }

        input[type=radio] {
            transform: scale(1.1);
            cursor: pointer;
        }

        button {
            margin-top: 16px;
            padding: 10px 18px;
            background: var(--accent);
            border: none;
            border-radius: 6px;
            color: var(--text);
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
        }

        button:hover {
            background: #e39195;
        }

        a {
            color: var(--accent);
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <h1>Pilih Isi - Buket Uang</h1>

    <form method="post" action="/custom/step/3">
        @csrf

        <!-- PILIH PECAHAN -->
        <div>
            <label><strong>Pilih Pecahan Uang</strong></label>
            <div class="option-group">
                @foreach($denominations as $d)
                    <label class="option-item">
                        <input type="radio" name="denomination" value="{{ $d }}" {{ old('denomination') == $d ? 'checked' : '' }}>
                        Rp {{ number_format($d, 0, ',', '.') }}
                    </label>
                @endforeach
            </div>
        </div>

        <!-- PILIH JUMLAH -->
        <div style="margin-top:16px">
            <label><strong>Pilih Jumlah Lembar</strong></label>
            <div class="option-group">
                @foreach($counts as $c)
                    <label class="option-item">
                        <input type="radio" name="count" value="{{ $c }}" {{ old('count') == $c ? 'checked' : '' }}>
                        {{ $c }}
                    </label>
                @endforeach
            </div>
        </div>

        <button type="submit">Lanjut ke Langkah 4</button>
    </form>

    <p><a href="/custom/reset">Batal & Kembali</a></p>

</body>

</html>