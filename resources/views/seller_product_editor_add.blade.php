<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Add Product Step</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 20px
        }

        .box {
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 6px
        }
    </style>
</head>

<body>
    <h1>Tambah - Step: {{ $step }}</h1>
    <div class="box">
        <p>Fitur tambah produk via helper halaman telah dinonaktifkan. Gunakan halaman utama <a
                href="/seller/product-editor">Editor Produk</a> atau fitur katalog.</p>
        <p style="margin-top:8px"><a href="/seller/product-editor">&larr; Kembali ke Editor Produk</a></p>
    </div>
</body>

</html>