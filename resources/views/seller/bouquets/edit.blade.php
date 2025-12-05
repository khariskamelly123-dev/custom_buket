<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    @include('partials.styles')
    <title>Edit Buket</title>
</head>

<body>
    <h1>Edit Buket</h1>
    <form method="post" action="/seller/manage/bouquets/{{ $bouquet->id }}/update">
        @csrf
        <div><label>Nama: <input name="name" value="{{ $bouquet->name }}" required></label></div>
        <div><label>Deskripsi: <textarea name="description">{{ $bouquet->description }}</textarea></label></div>
        <div><label>Harga: <input name="price" value="{{ $bouquet->price }}" required></label></div>
        <div><label>Image (URL): <input name="image" value="{{ $bouquet->image }}"></label></div>
        <div><label>Tersedia: <input type="checkbox" name="available" {{ $bouquet->available ? 'checked' : '' }}></label></div>
        <div><button type="submit">Simpan</button></div>
    </form>
    <p><a href="/seller/manage/bouquets">Batal</a></p>
</body>

</html>