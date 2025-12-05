<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    @include('partials.styles')
    <title>Tambah Buket</title>
</head>

<body>
    <h1>Tambah Buket</h1>
    <form method="post" action="/seller/manage/bouquets">
        @csrf
        <div><label>Nama: <input name="name" required></label></div>
        <div><label>Deskripsi: <textarea name="description"></textarea></label></div>
        <div><label>Harga: <input name="price" required></label></div>
        <div><label>Image (URL): <input name="image"></label></div>
        <div><label>Tersedia: <input type="checkbox" name="available" checked></label></div>
        <div><button type="submit">Simpan</button></div>
    </form>
    <p><a href="/seller/manage/bouquets">Batal</a></p>
</body>

</html>