<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Custom Buket - Langkah 5</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            /* Latar putih */
            margin: 0;
            padding: 0;
            color: #000000;
        }

        .container {
            max-width: 600px;
            background: #e9abae;
            /* Container pink */
            margin: 40px auto;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        h1 {
            text-align: center;
            margin-bottom: 24px;
            font-size: 24px;
            color: #000000;
        }

        label {
            display: block;
            margin-bottom: 14px;
            font-weight: bold;
            color: #000000;
        }

        input,
        select {
            width: 90%;
            /* Dipendekin */
            padding: 9px 12px;
            margin-top: 6px;
            border: 1px solid #000000;
            /* Border kecil */
            border-radius: 6px;
            font-size: 14px;
            background-color: #ffffff;
            color: #000000;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 18px;
            background-color: #ffffff;
            color: #000000;
            border: 2px solid #000000;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        button:hover {
            background-color: #f7f7f7;
        }

        .back-link {
            text-align: center;
            margin-top: 18px;
        }

        .back-link a {
            color: #000000;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Step 5: Identitas & Pembayaran</h1>

        <form method="post" action="/custom/step/5">
            @csrf

            <label>
                Nama:
                <input name="buyer_name" value="{{ $data['identity']['buyer_name'] ?? '' }}" required>
            </label>

            <label>
                Nomor HP:
                <input name="buyer_phone" value="{{ $data['identity']['buyer_phone'] ?? '' }}" required>
            </label>

            <label>
                Alamat:
                <input name="address" value="{{ $data['identity']['address'] ?? '' }}">
            </label>

            <label>
                Metode Pembayaran:
                <select name="payment_method" required>
                    <option value="COD">COD</option>
                    <option value="Transfer">Transfer</option>
                </select>
            </label>

            <button type="submit">Simpan Pesanan & Lanjutkan Pembayaran</button>
        </form>

        <div class="back-link">
            <a href="/custom/reset">Batal & Kembali</a>
        </div>
    </div>

</body>

</html>