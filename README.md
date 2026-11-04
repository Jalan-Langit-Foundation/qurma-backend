# Qurma 2027 Backend API

Backend web tabungan qurban Yayasan Jalan Langit, dibangun menggunakan Laravel dan Filament.

## 🚀 Cara Menjalankan Proyek (Lokal)
1. Clone repositori ini.
2. Salin file `.env.example` menjadi `.env` lalu sesuaikan konfigurasi database (`laragon`/`mysql`).
3. Jalankan perintah berikut di terminal:
   ```bash
   composer install
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve
