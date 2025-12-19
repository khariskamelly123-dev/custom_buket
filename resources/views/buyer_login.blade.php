<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Login Pembeli</title>
    @include('partials.styles')
    <style>
        .auth-card {
            max-width: 420px;
            margin: 40px auto;
            padding: 20px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px
        }

        .auth-card input {
            width: 100%;
            padding: 8px;
            margin-bottom: 8px
        }
    </style>
</head>

<body>
    <div class="auth-card">
        <h1>Masuk sebagai Pembeli</h1>
        @if(session('error'))
        <div style="color:#c53030">{{ session('error') }}</div>@endif
        <form method="post" action="/buyer/login">
            @csrf
            <input name="email" type="email" placeholder="Email" required>
            <input name="password" type="password" placeholder="Password" required>
            <div style="display:flex;gap:8px">
                <button class="btn" type="submit">Login</button>
                <a class="btn" href="/buyer/register" style="background:#718096">Daftar</a>
            </div>
        </form>
    </div>
</body>

</html>