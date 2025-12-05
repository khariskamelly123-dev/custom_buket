<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Pilih Isi Buket Jajan</title>
</head>

<body>
    <h1>Pilih Isi - Buket Jajan</h1>
    <form method="post" action="/custom/step/3">
        @csrf

        <div>
            <strong>Pilih Satu Pilihan Jajan</strong>
            <div style="margin-top:8px;display:flex;gap:12px;flex-wrap:wrap">
                @foreach($images as $img)
                    <label
                        style="display:block;border:1px solid #ddd;padding:8px;border-radius:6px;width:160px;text-align:center">
                        <div style="height:100px;margin-bottom:6px">
                            <img src="{{ asset('images/step3_jajan/' . $img) }}" alt="{{ $img }}"
                                style="max-width:100%;max-height:100%;object-fit:cover">
                        </div>
                        <div>
                            <input type="radio" name="filling_choice" value="{{ $img }}">
                            {{ pathinfo($img, PATHINFO_FILENAME) }}
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div style="margin-top:16px">
            <button type="submit">Lanjut ke Langkah 4</button>
        </div>
    </form>

    <p><a href="/custom/reset">Batal & Kembali</a></p>
</body>

</html>