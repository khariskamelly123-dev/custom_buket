<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    @include('partials.styles')
    <title>Kelola Buket</title>
</head>

<body>
    <h1>Kelola Buket</h1>
    <p><a href="/seller/manage">Kembali ke Dashboard</a> | <a href="/seller/manage/bouquets/create">Tambah Buket</a></p>
    @if(session('success'))
    <div style="color:green">{{ session('success') }}</div> @endif
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Available</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bouquets as $b)
                <tr>
                    <td>{{ $b->id }}</td>
                    <td>{{ $b->name }}</td>
                    <td>{{ $b->price }}</td>
                    <td>{{ $b->available ? 'Ya' : 'Tidak' }}</td>
                    <td>
                        <a href="/seller/manage/bouquets/{{ $b->id }}/edit">Edit</a>
                        <form method="post" action="/seller/manage/bouquets/{{ $b->id }}/delete" style="display:inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Hapus?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>