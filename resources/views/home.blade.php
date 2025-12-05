<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Selamat Datang</title>

    {{-- Styles tambahan dari folder partial jika diperlukan --}}
    @include('partials.styles')

    <style>
        :root {
            --bg: #ffffff;
            --surface: #fffafa;
            --accent: #e86f75;
            --muted: #7a7a7a;
            --text: #1b1b1b;
            --border: #e9d7d8;
            --radius: 8px;
            --gap: 16px;
            --container: 1100px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: var(--bg);
            color: var(--text);
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            padding: 32px;
            border-radius: var(--radius);
            text-align: center;
            box-shadow: 0 4px 14px rgba(16, 16, 16, 0.06);
        }

        .card h1 {
            margin-top: 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            margin: 8px;
            border-radius: 6px;
            text-decoration: none;
            background: #e9aeab;
            color: #fff;
            font-weight: bold;
        }

        .btn:hover {
            background: #ea9793;
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>Pilih Akses</h1>
        <p>Silakan pilih apakah Anda Pembeli atau Penjual.</p>
        <div>
            <a class="btn" href="/buyer">Pembeli</a>
            <a class="btn" href="/seller/login">Penjual</a>
        </div>
    </div>
</body>

</html>