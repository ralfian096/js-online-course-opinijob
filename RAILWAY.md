# Railway Setup

## Struktur

- Source frontend tetap ada di `HTML/`.
- Laravel tetap ada di `API/`.
- Saat deploy, `Dockerfile` akan menyalin isi `HTML/` ke `API/public/`.

## Local Development

- `npm run dev:split`
  Menjalankan host HTML di `http://127.0.0.1:5500` dan Laravel API di `http://127.0.0.1:8000`.

- `npm run sync:html`
  Menyalin isi `HTML/` ke `API/public/` satu kali.

- `npm run dev:mono`
  Memantau perubahan pada `HTML/`, sinkron ke `API/public/`, lalu menjalankan Laravel di `http://127.0.0.1:8000`.

## Deploy Ke Railway

1. Deploy repo ini dari root project.
2. Railway akan memakai `Dockerfile` di root.
3. Tambahkan database Railway, lalu isi environment variable Laravel.
4. Generate public domain untuk service app.

## Environment Variable Minimum

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://domain-railway-anda`
- `APP_KEY=base64:...`
- `DB_CONNECTION=mysql` atau `pgsql`
- `DB_HOST=...`
- `DB_PORT=...`
- `DB_DATABASE=...`
- `DB_USERNAME=...`
- `DB_PASSWORD=...`

## Migrasi Database

- Default container tidak menjalankan migrasi otomatis.
- Jika ingin migrasi otomatis saat start, set `RUN_MIGRATIONS=true`.
- Untuk production, tetap lebih aman menjalankan migrasi dengan kontrol yang jelas saat deploy.
