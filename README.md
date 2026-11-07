# Üretim Takip Sistemi / Production Tracking System

Laravel tabanlı, üretim süreçlerinin takibini ve raporlanmasını sağlayan bir web uygulaması.

*A Laravel-based web application for tracking and reporting production processes.*

---

## 🇹🇷 Türkçe

### Hakkında

Bu proje, üretim sürecindeki kayıtların tek bir yerden yönetilmesi, listelenmesi ve raporlanması için geliştirilmiştir. Veriler sunucu taraflı (server-side) tablolarla hızlı bir şekilde listelenir, filtrelenir ve aranır; raporlar Word (.docx) belgesi olarak oluşturulabilir.

### Özellikler

- Üretim kayıtlarının eklenmesi, düzenlenmesi ve silinmesi (CRUD)
- DataTables ile arama, sıralama, filtreleme ve sayfalama
- Word (.docx) formatında rapor/belge oluşturma
- Kullanıcı girişi ve yetkilendirme
- Üretim durumu ve ilerleme takibi

### Kullanılan Teknolojiler

| Alan | Teknoloji |
|---|---|
| Backend | PHP 8.2+, Laravel 11 |
| Tablolar | [yajra/laravel-datatables-oracle](https://github.com/yajra/laravel-datatables) |
| Word belgeleri | [PHPOffice/PHPWord](https://github.com/PHPOffice/PHPWord) |
| Veritabanı | MySQL
| Frontend | Blade, Bootstrap, JavaScript, jQuery
| Test | PHPUnit |

### Gereksinimler

- PHP 8.2 veya üzeri
- Composer
- MySQL

### Kurulum

```bash
# 1. Repoyu klonla
git clone https://github.com/aniluzuner/laravel-production-tracking.git
cd laravel-production-tracking

# 2. PHP bağımlılıklarını yükle
composer install

# 3. Ortam dosyasını oluştur ve uygulama anahtarını üret
cp .env.example .env
php artisan key:generate

# 4. .env dosyasında veritabanı bilgilerini düzenle (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 5. Tabloları oluştur
php artisan migrate

# 6. Uygulamayı başlat
php artisan serve
```

Uygulama varsayılan olarak `http://localhost:8000` adresinde çalışır.

### Klasör Yapısı

```
app/          Controller, Model ve uygulama mantığı
database/     Migration, seeder ve factory dosyaları
resources/    Blade şablonları, CSS ve JS dosyaları
routes/       Web rotaları
public/       Dışarıya açık dosyalar
```

### Notlar

- `.env` dosyası güvenlik nedeniyle repoya dahil edilmemiştir. Kendi ortamında `.env.example` dosyasından oluşturmalısın.
- `vendor/` ve `node_modules/` klasörleri `composer install` ve `npm install` ile yeniden oluşturulur.

---

## 🇬🇧 English

### About

This project is built to manage, list and report production records from a single place. Data is listed, filtered and searched quickly through server-side tables, and reports can be generated as Word (.docx) documents.

### Features

- Create, edit and delete production records (CRUD)
- Search, sort, filter and pagination with DataTables
- Report/document generation in Word (.docx) format
- User authentication and authorization
- Production status and progress tracking

### Tech Stack

- **Backend:** PHP 8.2+, Laravel 11
- **Tables:** yajra/laravel-datatables-oracle
- **Word documents:** PHPOffice/PHPWord
- **Database:** MySQL
- **Testing:** PHPUnit

### Installation

```bash
git clone https://github.com/aniluzuner/laravel-production-tracking.git
cd laravel-production-tracking
composer install
cp .env.example .env
php artisan key:generate
# configure your database in .env
php artisan migrate
php artisan serve
```

---

## Lisans / License

Bu proje [MIT](https://opensource.org/licenses/MIT) lisansı ile paylaşılmıştır. / This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).