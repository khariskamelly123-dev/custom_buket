<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    @include('partials.styles')
    <title>Seller - Orders</title>
</head>

<body>
    <div style="display:flex;justify-content:space-between;align-items:center">
        <h1>Daftar Pesanan</h1>
        <div style="display:flex;gap:8px">
            <a href="/seller/catalog" style="
                    display:inline-block;
                    padding:8px 12px;
                    background:#e9aeab;
                    color:#fff;
                    border-radius:4px;
                    text-decoration:none;
                    margin-top:20px;       /* TURUNKAN */
                    margin-left:-120px;     /* GESER KIRI */
                ">
                Edit Katalog
            </a>


        </div>
    </div>
    @if(session('success'))
        <div style="color:#e86f75;margin-bottom:120px; margin-left:4px;">
            {{ session('success') }}
        </div>
    @endif


    <form method="get" action="/seller/orders" style="margin:12px 0">
        <label> Cari Nomor Pesanan: <input type="text" name="q" value="{{ $q ?? '' }}"
                placeholder="Masukkan nomor pesanan"></label>
        <button type="submit">Cari</button>
        @if(!empty($q)) <a href="/seller/orders" style="margin-left:8px">Reset</a> @endif
    </form>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Pemesan</th>
                <th>Metode Pembayaran</th>
                <th>Status</th>
                <th>Nomor Pesanan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $o)
                <tr>
                    <td>{{ $o->id }}</td>
                    <td>{{ $o->buyer_name }}</td>
                    <td>{{ $o->payment_method ?? '-' }}</td>
                    <td>{{ $o->status ?? 'new' }}
                        <form method="post" action="/seller/orders/{{ $o->id }}/status"
                            style="display:inline;margin-left:8px">
                            @csrf
                            @if($o->status === 'created')
                                <input type="hidden" name="status" value="new">
                                <button type="submit">Tandai Belum Dibuat</button>
                            @else
                                <input type="hidden" name="status" value="created">
                                <button type="submit">Tandai Dibuat</button>
                            @endif
                        </form>
                    </td>
                    <td><a href="/seller/orders/{{ $o->id }}">{{ $o->order_number }}</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Buyer quick-order form removed as requested -->
</body>

</html>