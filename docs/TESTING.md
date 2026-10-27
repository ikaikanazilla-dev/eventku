# Testing

Jalankan test dengan:

```powershell
php artisan test
```

Test menggunakan SQLite in-memory pada `phpunit.xml`, sehingga tidak pernah mereset database MySQL development. Cakupan penting meliputi akses role, pendaftaran peserta, duplikasi, kuota, penerbitan tiket, ownership tiket, dan double check-in.

