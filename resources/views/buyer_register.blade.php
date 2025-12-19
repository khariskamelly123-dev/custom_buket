<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Daftar Pembeli</title>
    @include('partials.styles')
    <style>
        .auth-card {
            max-width: 520px;
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
        <h1>Registrasi Pembeli</h1>
        @if($errors->any())
        <div style="color:#c53030">{{ $errors->first() }}</div>@endif
        <form method="post" action="/buyer/register">
            @csrf
            <input name="name" type="text" placeholder="Nama lengkap" required>
            <input name="email" type="email" placeholder="Email" required>
            <input name="password" type="password" placeholder="Password" required>
            <input name="password_confirmation" type="password" placeholder="Konfirmasi Password" required>
            <div style="display:flex;gap:8px">
                <button class="btn" type="submit">Daftar & Masuk</button>
                <a class="btn" href="/buyer/login" style="background:#718096">Sudah punya akun?</a>
            </div>
        </form>
    </div>
</body>

</html>