<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    @include('partials.styles')
    <title>Penjual Login</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            /* putih */
            margin: 0;
            padding: 0;
            color: #000000;
        }

        h1 {
            text-align: center;
            margin-top: 40px;
            color: #000000;
        }

        .container {
            max-width: 400px;
            background: #e9abae;
            /* pink */
            margin: 30px auto;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        label {
            display: block;
            margin-bottom: 16px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #000000;
            border-radius: 6px;
            background: #ffffff;
            color: #000000;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #ffffff;
            color: #000000;
            border: 2px solid #000000;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }

        button:hover {
            background: #e9aeab;
        }

        .error-text {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <h1>Penjual Login</h1>

    <div class="container">

        @if($errors->any())
            <div class="error-text">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="/seller/login">
            @csrf

            <label>
                Email:
                <input name="email" type="email" required>
            </label>

            <label>
                Password:
                <input name="password" type="password" required>
            </label>

            <button type="submit" class="btn-login">Login</button>

            <style>
                .btn-login {
                    width: 100%;
                    padding: 12px 0;
                    border-radius: 10px;
                    border: 2px solid #000;
                    background: white;

                    display: flex;
                    /* Rahasia utama */
                    justify-content: center;
                    /* Tengah horizontal */
                    align-items: center;
                    /* Tengah vertical */

                    font-size: 16px;
                    font-weight: bold;
                    cursor: pointer;
                }
            </style>



        </form>

    </div>

</body>

</html>