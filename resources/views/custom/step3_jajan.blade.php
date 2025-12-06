<!doctype html>
<html>

<style>
    /* ====== Global ====== */
    body {
        font-family: Arial, sans-serif;
        background-color: #ffffff;
        color: #000000;
        margin: 0;
        padding: 20px;
    }

    h1 {
        margin-bottom: 20px;
    }

    /* ====== Wrapper pilihan ====== */
    .choices-wrapper {
        margin-top: 8px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: center;
    }

    /* ====== Card Pilihan ====== */
    .choice-card {
        display: block;
        border: 2px solid #ddd;
        padding: 10px;
        border-radius: 10px;
        width: 160px;
        text-align: center;
        background-color: #ffffff;
        cursor: pointer;
        transition: 0.25s;
    }

    .choice-card:hover {
        border-color: #e9aeab;
        transform: translateY(-3px);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }

    /* ====== Card Saat Terpilih ====== */
    .choice-card input:checked+span,
    .choice-card.selected {
        border-color: #e9aeab !important;
        background-color: #ffecec;
    }

    /* ====== Gambar ====== */
    .choice-card .img-box {
        height: 110px;
        margin-bottom: 6px;
    }

    .choice-card img {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
        border-radius: 6px;
    }

    /* ====== Tombol ====== */
    button {
        background-color: #e9aeab;
        color: #000000;
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.25s;
        font-size: 15px;
    }

    button:hover {
        background-color: #d99693;
    }

    /* ====== Link Reset ====== */
    a {
        color: #000000;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>

<head>
    <meta charset="utf-8">
    <title>Pilih Isi Buket Jajan</title>
    <link rel="stylesheet" href="/css/step3.css"> <!-- Pastikan CSS ini ditaruh -->
</head>

<body>
    <h1>Pilih Isi - Buket Jajan</h1>

    <form method="post" action="/custom/step/3">
        @csrf

        <div>
            <strong>Pilih Satu Pilihan Jajan</strong>

            <div class="choices-wrapper">
                @foreach($images as $img)
                    <label class="choice-card">
                        <div class="img-box">
                            <img src="{{ asset('images/step3_jajan/' . $img) }}" alt="{{ $img }}">
                        </div>

                        <input type="radio" name="filling_choice" value="{{ $img }}">
                        <div>{{ pathinfo($img, PATHINFO_FILENAME) }}</div>
                    </label>
                @endforeach
            </div>
        </div>

        <div style="margin-top:16px;text-align:center">
            <button type="submit">Lanjut ke Langkah 4</button>
        </div>
    </form>

    <p style="text-align:center;margin-top:10px">
        <a href="/custom/reset">Batal & Kembali</a>
    </p>

</body>

</html>