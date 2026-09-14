# i remember all the places — updated scrapbook

Laravel + SQLite + Blade starter/source package for the **i remember all the places** app.

## Fitur versi ini
- Register/login/logout lokal (tanpa Google Login)
- Dashboard bergaya scrapbook
- Folder/scrapbook
- Memory berdasarkan tanggal/event
- **Satu memory bisa memiliki sampai 30 foto**
- Preview banyak foto sebelum upload
- Foto lama tetap kompatibel
- Pencarian lokasi Indonesia menggunakan OpenStreetMap Nominatim
- Koordinat dan link Google Maps otomatis
- Catatan/cerita
- Hapus memory dan folder
- Responsive untuk desktop/mobile

## Penting untuk project yang sudah pernah dijalankan
ZIP ini berisi source code untuk ditempel ke project Laravel yang sudah kamu buat. **Tidak menyertakan `vendor/`**.

Jika database kamu sudah berisi folder/memory, jangan gunakan `php artisan migrate:fresh`.

Jalankan:

```powershell
php artisan migrate
php artisan storage:link
php artisan optimize:clear
php artisan serve
```

Migration `create_memory_photos_table` dibuat aman untuk kondisi ketika tabel `memory_photos` sudah terlanjur ada tetapi migration belum tercatat.

## Instalasi project Laravel baru

```powershell
composer create-project laravel/laravel i-remember-all-the-places
```

Salin isi ZIP ini ke project tersebut dan timpa file yang sama, lalu siapkan SQLite:

```powershell
New-Item database/database.sqlite -ItemType File
php artisan migrate
php artisan storage:link
php artisan serve
```

Pastikan `.env` menggunakan:

```env
DB_CONNECTION=sqlite
```

## Multiple photo

Form memory menggunakan:

```text
photos[]
```

Satu memory dapat memiliki hingga 30 foto, maksimal 15 MB per foto.

Struktur:

```text
folders
  └── memories
       └── memory_photos
```
