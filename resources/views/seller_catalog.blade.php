<!doctype html>
<html>

<head>
    <!-- Tombol kembali ke dashboard -->
    <a href="/seller/manage/orders" style="
               display:inline-block;
               background:#e9aeab;
               color:#1b1b1b;
               padding:8px 14px;
               border-radius:6px;
               font-weight:600;
               margin:20px;
               text-decoration:none;
               border:1px solid #d8a2a0;
           ">
        ← Kembali ke nomor pesanan
    </a>
    <meta charset="utf-8">

    @include('partials.styles')

    <style>
        :root {
            --bg: #fefefe;
            --surface: #fff0f3;
            --accent: #e86f75;
            --text: #1b1b1b;
            --border: #e9d7d8;
        }

        body {
            background: var(--bg);
            font-family: Inter, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: var(--surface);
            border-radius: 10px;
            border: 1px solid var(--border);
        }

        input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            margin-bottom: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Katalog</h2>

        @if(session('success'))
            <div style="color:#e86f75;margin-bottom:12px; margin-left:4px;">
                {{ session('success') }}
            </div>
        @endif


        <h3>Daftar Produk</h3>

        <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:18px">
            @foreach($bouquets as $b)
                <div
                    style="display:flex;gap:12px;align-items:center;border:1px solid var(--border);padding:10px;border-radius:8px;background:#fff">
                    <div style="width:120px;height:80px;flex:0 0 120px">
                        @if($b->image)
                            <img src="{{ $b->image }}" style="width:100%;height:100%;object-fit:cover;border-radius:6px">
                        @else
                            <div
                                style="width:100%;height:100%;background:#f5d6d7;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#666">
                                No Image</div>
                        @endif
                    </div>

                    <div style="flex:1">
                        @if(request('edit') == $b->id)
                            <form method="post" action="/seller/bouquets/{{ $b->id }}/update" enctype="multipart/form-data"
                                style="display:flex;flex-direction:column;gap:8px">
                                @csrf
                                <label>Nama
                                    <input name="name" type="text" required value="{{ $b->name }}">
                                </label>

                                <label>Deskripsi
                                    <textarea name="description" rows="3">{{ $b->description }}</textarea>
                                </label>

                                <label>Harga
                                    <input name="price" type="number" required value="{{ $b->price }}">
                                </label>

                                <label>Gambar (jpg/png)
                                    <input name="image_file" type="file" accept="image/*">
                                </label>

                                <label style="display:flex;align-items:center;gap:8px">
                                    <input type="checkbox" name="available" {{ $b->available ? 'checked' : '' }}> Tersedia
                                </label>

                                <div style="display:flex;gap:8px">
                                    <button type="submit" class="btn">Simpan</button>
                                    <a href="/seller/catalog" class="btn" style="background:#718096">Batal</a>
                                    <a href="/seller/manage" class="btn" style="background:#4A5568">Kembali ke Dashboard</a>
                                </div>
                            </form>
                        @else
                            <div style="font-weight:700">{{ $b->name }}</div>
                            <div style="color:var(--muted)">Rp {{ number_format($b->price, 0, ',', '.') }}</div>
                            <div style="margin-top:6px">{{ $b->description }}</div>
                        @endif
                    </div>

                    <div style="display:flex;flex-direction:column;gap:8px">
                        @if(request('edit') == $b->id)
                            <!-- editing: actions are in the form -->
                        @else
                            <a class="btn" href="/seller/catalog?edit={{ $b->id }}">Edit</a>

                            <form method="post" action="/seller/manage/bouquets/{{ $b->id }}/delete"
                                onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                <input type="hidden" name="return_edit" value="{{ request('edit') }}">
                                <button type="submit" class="btn" style="background:#c53030">Hapus</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <h3>Tambah Produk Baru</h3>

        <form method="post" action="/seller/bouquets" enctype="multipart/form-data"
            style="display:flex;flex-direction:column;gap:8px">
            @csrf

            <label>Nama
                <input name="name" type="text" required>
            </label>

            <label>Deskripsi
                <textarea name="description" rows="3"></textarea>
            </label>

            <label>Harga
                <input name="price" type="number" required>
            </label>

            <label>Gambar (jpg/png)
                <input name="image_file" type="file" accept="image/*">
            </label>

            <label style="display:flex;align-items:center;gap:8px">
                <input type="checkbox" name="available" checked> Tersedia
            </label>

            <div>
                <button type="submit" class="btn">Simpan Produk</button>
            </div>
        </form>

    </div>
</body>

</html>