<style>
    /* Shared site styles (extracted from inlined views) */
    :root {
        --bg: #ffffff;
        --surface: #fffafa;
        --accent: #e86f75;
        --muted: #7a7a7a;
        --text: #1b1b1b;
        --border: #e9d7d8;
        --radius: 8px;
        --gap: 16px;
        --container: 1100px;
    }



    .thumb {
        width: 140px;
        height: 140px;
        /* kotak */
        border-radius: 8px;
        overflow: hidden;
        background: #eee;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* biar gambarnya nggak ketarik */
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
        font-size: 16px;
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

    h1,
    h2,
    h3 {
        margin: 0 0 8px 0;
        color: var(--text);
        line-height: 1.2
    }

    h1 {
        font-size: clamp(1.6rem, 2.2vw, 2.2rem)
    }

    h2 {
        font-size: clamp(1.25rem, 1.6vw, 1.6rem)
    }

    h3 {
        font-size: 1.125rem
    }

    .container {
        max-width: var(--container);
        margin: 0 auto;
        padding: var(--gap)
    }

    .header {
        background: linear-gradient(90deg, rgba(232, 111, 116, 0.08), rgba(232, 111, 116, 0.02));
        padding: 12px 16px;
        border-radius: var(--radius);
        display: inline-block
    }

    .card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px;
        box-shadow: 0 4px 14px rgba(16, 16, 16, 0.06);
        margin-bottom: 18px
    }

    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 12px
    }

    .card .thumb {
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 6px
    }

    .card .thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover
    }

    .card .label {
        text-align: center;
        margin-top: 8px;
        font-size: 0.95rem;
        color: var(--muted)
    }

    .placeholder {
        background: #fff0f0;
        color: var(--muted);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px;
        border-radius: 6px
    }

    .btn,
    button,
    input[type=button],
    input[type=submit],
    a.action-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 6px;
        border: none;
        background: var(--accent);
        color: #fff;
        font-weight: 600;
        cursor: pointer
    }

    .btn.secondary,
    button.secondary {
        background: #f5f5f5;
        color: var(--text);
        border: 1px solid var(--border)
    }

    .btn:active {
        transform: translateY(1px)
    }

    input[type=text],
    input[type=file],
    textarea,
    select {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid var(--border);
        background: #fff;
        color: var(--text)
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: var(--muted)
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: transparent
    }

    table th,
    table td {
        padding: 10px;
        border: 1px solid var(--border);
        text-align: left;
        color: var(--text)
    }

    table thead th {
        background: #fff7f7
    }

    .muted,
    small {
        color: var(--muted);
        font-size: 0.95rem
    }

    .text-center {
        text-align: center
    }

    .flex {
        display: flex
    }

    .btn.dark,
    button.dark,
    a.btn.dark {
        background: #0b0b0b;
        color: #fff;
        padding: 12px 20px;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 700
    }

    .product-media {
        min-width: 260px;
        min-height: 320px;
        overflow: hidden;
        border-radius: 8px
    }

    .product-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block
    }

    .product-name {
        font-size: 1.5rem;
        margin: 0 0 8px 0
    }

    .product-price {
        font-size: 1.15rem;
        color: var(--text);
        font-weight: 700
    }

    .product-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: flex-end;
        gap: 8px
    }

    .flex-center {
        display: flex;
        align-items: center;
        justify-content: center
    }

    .gap-sm {
        gap: 8px
    }

    .gap {
        gap: var(--gap)
    }

    @media (max-width:640px) {
        .container {
            padding: 12px
        }

        .card .thumb {
            height: 80px
        }

        h1 {
            font-size: 1.4rem
        }
    }

    .box {
        padding: 12px;
        border-radius: var(--radius);
        background: var(--surface);
        border: 1px solid var(--border)
    }

    .action-link {
        padding: 6px 10px;
        border-radius: 6px;
        background: var(--accent);
        color: #fff
    }

    .sr-only {
        position: absolute !important;
        height: 1px;
        width: 1px;
        overflow: hidden;
        clip: rect(1px, 1px, 1px, 1px);
        white-space: nowrap
    }

    /* Product card layout (image left, info right) */
    .cards {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px
    }

    .product-card {
        display: flex;
        gap: 16px;
        align-items: stretch
    }

    .product-media {
        flex: 0 0 300px;
        border-radius: 6px;
        overflow: hidden
    }

    .product-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block
    }

    .product-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between
    }

    .product-info .meta {
        padding: 6px 0
    }

    .product-name {
        font-size: 1.4rem;
        margin: 0;
        color: var(--text)
    }

    .product-price {
        font-size: 1.1rem;
        color: var(--muted);
        text-align: right
    }

    .product-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: flex-end;
        gap: 8px
    }

    @media (max-width:900px) {
        .product-media {
            flex-basis: 200px
        }
    }

    @media (max-width:640px) {
        .product-card {
            flex-direction: column
        }

        .product-media {
            height: 200px;
            flex-basis: auto
        }

        .product-price {
            text-align: left
        }

        .product-actions {
            justify-content: flex-start
        }
    }
</style>