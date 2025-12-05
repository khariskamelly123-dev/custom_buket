<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    @include('partials.styles')
    <title>Pilih Jenis Buket</title>

    <style>
        body {
            font-family: "Arial", sans-serif;
            background: #ffffff;
            color: #000000;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 650px;
            margin: 40px auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 35px;
            font-size: 28px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .option-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px;
            border-radius: 15px;
            border: 2px solid #e9aeab;
            background: #e9aeab20;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .option-card:hover {
            background: #e9aeab50;
        }

        .option-card input[type="radio"] {
            transform: scale(1.3);
            accent-color: #e9aeab;
            cursor: pointer;
        }

        button {
            margin-top: 15px;
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
            font-size: 15px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Pilih Jenis Buket</h1>

        <form method="post" action="/custom/step/1">
            @csrf

            @foreach($types as $key => $label)
                <label class="option-card">
                    <input type="radio" name="bouquet_type" value="{{ $key }}">
                    <span>{{ $label }}</span>
                </label>
            @endforeach

            <button type="submit">Lanjut ke Langkah 2</button>
        </form>

        <div class="back-link">
            <a href="/custom/reset">Batal & Kembali</a>
        </div>
    </div>

</body>

</html>