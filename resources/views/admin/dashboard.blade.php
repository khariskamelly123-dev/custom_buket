<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    @include('partials.styles')
    <title>Penjual Dashboard</title>
    <style>
        .box {
            border: 1px solid #ddd;
            padding: 16px;
            border-radius: 6px;
            margin: 12px
        }
    </style>
</head>

<body>
    <h1>Penjual Dashboard</h1>
    <p><a href="/seller/logout">Logout</a></p>
    <div class="box">
        <h3>Bouquet Management</h3>
        <p><a href="/seller/manage/bouquets">Kelola Buket</a></p>
    </div>
    <div class="box">
        <h3>Pesanan</h3>
        <p><a href="/seller/manage/orders">Lihat Pesanan Masuk</a></p>
    </div>
</body>

</html>