<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    @include('partials.styles')
    :root {
    --bg: #ffffff;
    --surface: #fffafa;
    --accent: #e86f75;
    --muted: #7a7a7a;
    --text: #1b1b1b;
    --border: #e9d7d8;
    --radius: 8px;
    --gap: 16px;
    --container: 1100px
    }

    * {
    box-sizing: border-box
    }

    html,
    body {
    height: 100%
    }

    body {
    margin: 0;
    font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
    background: var(--bg);
    color: var(--text);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    line-height: 1.45;
    font-size: 16px
    }

    img {
    max-width: 100%;
    height: auto;
    display: block
    }

    a {
    color: var(--accent);
    text-decoration: none
    }

    a:hover {
    text-decoration: underline
    }

    .container {
    max-width: var(--container);
    margin: 0 auto;
    padding: var(--gap)
    }

    .card {
    background: var(--surface);
    @include('partials.styles')