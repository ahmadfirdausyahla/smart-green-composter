# Smart Green Composter — ZeroWaste Green Platform

> **Platform Terpadu Pengelolaan Sampah Organik Cerdas Berbasis IoT & Desentralisasi Komunitas RT/RW**

---

## 1. Ringkasan Eksekutif & Desain Sistem

Smart Green Composter adalah aplikasi web full-stack terintegrasi yang dibangun menggunakan **Laravel 12**, **Tailwind CSS**, dan **Alpine.js**. Aplikasi ini mengelola siklus operasional mesin pengomposan cepat (4 jam) berbasis mikrokontroler ESP32, sensor suhu DHT22, timbangan LoadCell HX711, dan pemutus sirkuit keselamatan termal otomatis.

Visual identity aplikasi mengusung tema **White – Green – Clean – Modern – Professional**:
- **White**: `#FFFFFF`
- **Primary Green**: `#16834A`
- **Dark Green**: `#105C38`
- **Soft Green**: `#EAF5EE`
- **Main Text**: `#17251D`
- **Secondary Text**: `#68756D`
- **Border**: `#E6EBE7`
- **Background**: `#F7F9F7`

---

## 2. Struktur Modul & Peran Pengguna (RBAC)

Hierarki otorisasi ketat berbasis regionalitas:

| Peran | Ruang Lingkup Data | Menu Akses Utama |
| :--- | :--- | :--- |
| **Master Admin** | Seluruh Indonesia (Nasional) | Dashboard Nasional, Monitoring & Remote, Persetujuan Operasi, Kondisi Alat, Laporan & Analitik, Pendaftaran RT/RW, Inventaris & Distribusi, Pengajuan Alat, Jadwal Petugas, Knowledge Base AI, Audit Log Tamper-Evident, Simulator IoT |
| **Admin Kota** | Kota / Kabupaten Tertentu | Dashboard Kota, Monitoring & Remote (dengan alur izin ke RT/RW), Persetujuan Operasi, Kondisi Alat, Laporan Kota, Verifikasi Pendaftaran RT/RW, Inventaris Kota & Distribusi ke RT/RW, Pengajuan Alat ke Pusat, Knowledge Base, Audit Log Kota |
| **Admin RT/RW** | Lingkungan RT/RW Tertentu | Dashboard RT/RW, Monitoring & Remote Langsung, Persetujuan Operasi Petugas Lapangan, Kondisi Alat & Pelaporan, Laporan RT/RW, Pengajuan Unit Tambahan, Kelola Jadwal Petugas & Akun Lapangan |
| **Admin Lapangan** | Unit RT/RW Tertugas | **Hanya 2 Menu:**<br>1. **Monitoring** (Telemetri real-time, pengajuan izin START/STOP, jadwal piket hari ini, dan riwayat operasi pribadi)<br>2. **Kondisi Alat** (Pelaporan gangguan/kerusakan mesin) |

---

## 3. Arsitektur Layanan (Services Architecture)

Aplikasi mengisolasi logika eksternal menggunakan pola provider independen:

```
app/
├── Services/
│   ├── IoT/
│   │   ├── Contracts/
│   │   │   └── IoTProviderInterface.php
│   │   ├── DTOs/
│   │   │   ├── TelemetryReadingDTO.php
│   │   │   ├── DeviceCommandDTO.php
│   │   │   └── CommandAckDTO.php
│   │   ├── Providers/
│   │   │   ├── MockIoTProvider.php
│   │   │   └── HttpIoTProvider.php
│   │   └── IoTService.php
│   ├── AI/
│   │   ├── Contracts/
│   │   │   └── AIProviderInterface.php
│   │   ├── Providers/
│   │   │   ├── MockAIProvider.php
│   │   │   └── OpenAIAIProvider.php
│   │   ├── KnowledgeBaseService.php
│   │   └── AIService.php
│   └── Audit/
│       └── AuditService.php
```

### 3.1 Integrasi IoT (`IoTService`)
- **Mock Mode (`IOT_DRIVER=mock`)**: Mensimulasikan fluktuasi sensor termal dan timbangan, timer 240 menit, auto-acknowledge perintah `START`, `STOP`, `EMERGENCY_STOP`, `SET_TIMER`, `SET_TEMP_LIMIT`, serta deteksi cutoff darurat 70°C.
- **API Mode (`IOT_DRIVER=api`)**: Menghubungkan sistem ke server gateway IoT fisik melalui HTTP REST API dengan bearer token dan timeout protektif tanpa merombak kode controller/view.

### 3.2 Integrasi AI & Chatbot Edukasi Publik (`AIService`)
- Publik dapat mengakses asisten AI pada seluruh halaman website tanpa harus login.
- **RAG (Retrieval-Augmented Generation)**: Mencari konteks relevan dari tabel `knowledge_chunks` dan `knowledge_documents`.
- **Guardrails Ketat**: Secara otomatis menolak perintah fisik mesin (*"matikan mesin"*, *"stop"*) atau permintaan data kredensial (*"password"*, *"api_key"*).
- **Driver**: Mendukung `mock` (jawaban edukasi berbasis konteks resmi) dan `openai` (GPT-4o-mini).

### 3.3 Audit Log Tamper-Evident Append-Only (`AuditService`)
- Setiap aksi administratif, pengiriman perintah kendali IoT, dan perubahan konfigurasi dicatat secara permanen.
- **Rantai Hash SHA-256 (Hash Chaining)**: Menautkan hash record sebelumnya dengan data saat ini.
- **Proteksi Immutabilitas**: Model Eloquent menolak mutasi (`update`) dan penghapusan (`delete`) dengan melemparkan `RuntimeException`.
- **Verifikasi Kriptografis**: Endpoint `/admin/audit-logs/verify` memindai seluruh rantai hash dan memvalidasi keutuhan data terhadap manipulasi database langsung.

---

## 4. 10 Halaman Publik (Tanpa Autentikasi)

1. **Beranda (`/`)**: Hero informasi, metrik capaian nasional, cara kerja singkat, dan cuplikan peta GIS.
2. **Tentang Smart Green Composter (`/tentang`)**: Latar belakang krisis sampah organik dan 3 pilar solusi ZeroWaste Green.
3. **Cara Kerja (`/cara-kerja`)**: Alur terstruktur 4 tahap: Input & Penimbangan, Izin Operasi, Pencacahan & Pemanasan Termofilik, dan Perhitungan Reduksi Massa.
4. **Teknologi & Fitur (`/teknologi`)**: Spesifikasi ESP32, LoadCell HX711 24-bit, Sensor DHT22, dan sistem proteksi suhu 70°C.
5. **Peta Sebaran GIS (`/gis` & `/peta-sebaran`)**: Peta interaktif Leaflet / OpenStreetMap persebaran mesin aktif tanpa membocorkan alamat pribadi warga.
6. **Dampak Lingkungan (`/statistik` & `/dampak-lingkungan`)**: Rekapitulasi reduksi massa dan riwayat pemrosesan terbuka.
7. **Berita & Informasi (`/berita`)**: Publikasi artikel edukasi pemilahan dan kabar implementasi lapangan.
8. **FAQ (`/faq`)**: Tanya-jawab interaktif berbasis accordion seputar batas keselamatan dan jenis sampah yang diizinkan.
9. **Kontak Kami (`/kontak`)**: Informasi sekretariat, hotline pengaduan, dan formulir permohonan demo/sosialisasi RT/RW.
10. **Public AI Chatbot (`/chatbot` & Floating Widget)**: Antarmuka asisten virtual edukasi publik bertenaga RAG.

---

## 5. Kredensial Akun Pengembangan (Seeded Accounts)

Semua akun awal menggunakan kata sandi: `password`

| Peran | Nama Akun | Email Login | Cakupan Wilayah |
| :--- | :--- | :--- | :--- |
| **Master Admin** | Budi Santoso | `master@zerowaste.id` | Nasional (Seluruh Indonesia) |
| **Admin Kota Bogor** | Rahmat Hidayat | `admin.bogor@zerowaste.id` | Kota Bogor, Jawa Barat |
| **Admin Kota Bandung** | Cecep Ridwan | `admin.bandung@zerowaste.id` | Kota Bandung, Jawa Barat |
| **Admin RT/RW** | Agus Wijaya | `admin.rtrw@zerowaste.id` | RT 03 / RW 05 Babakan, Bogor Tengah |
| **Admin Lapangan** | Dedi Kurniawan | `petugas@zerowaste.id` | RT 03 / RW 05 Babakan, Bogor Tengah |

---

## 6. Petunjuk Instalasi & Menjalankan Proyek

### Kebutuhan Sistem:
- PHP >= 8.2 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`)
- MySQL / MariaDB (Database Server)
- Composer >= 2.x
- Node.js >= 18.x & npm

### Langkah Setup:
```bash
# 1. Salin konfigurasi environment
cp .env.example .env

# 2. Pasang dependensi PHP dan Node.js
composer install
npm install

# 3. Generate Application Key
php artisan key:generate

# 4. Migrasi dan Seeding Database
php artisan migrate:fresh --seed

# 5. Kompilasi aset CSS dan JavaScript
npm run build

# 6. Jalankan Server Pengembangan
php artisan serve
```

Aplikasi dapat diakses melalui browser di: `http://127.0.0.1:8000`

---

## 7. Konfigurasi IoT & AI Provider

### Beralih Mode IoT Mock ke API Nyata:
Di dalam berkas `.env`:
```env
# Mode Pengujian (Default)
IOT_DRIVER=mock

# Mode API Server IoT Fisik (Masa Depan)
IOT_DRIVER=api
IOT_API_BASE_URL=https://iot.zerowastegreen.id
IOT_API_TOKEN=rahasia-token-gateway-iot
IOT_WEBHOOK_SECRET=rahasia-webhook-hmac
IOT_API_TIMEOUT=10
```

### Mengonfigurasi Layanan AI Chatbot:
Di dalam berkas `.env`:
```env
# Mode Mock RAG (Default)
AI_DRIVER=mock

# Mode OpenAI / LLM Eksternal
AI_DRIVER=openai
AI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxxxxx
AI_BASE_URL=https://api.openai.com/v1
AI_MODEL=gpt-4o-mini
AI_TIMEOUT=30
```

---

## 8. Menjalankan Pengujian Otomatis (Automated Tests)

Suite pengujian komprehensif mencakup otentikasi, perbatasan regional, integritas hash log audit, kalkulasi reduksi massa, ekspor CSV, simulator IoT, dan batas pengamanan AI:

```bash
# Menjalankan seluruh test suite
php vendor/phpunit/phpunit/phpunit
```
*Hasil: 33 pengujian passed (115 assertions).*

---

## 9. Catatan & Batasan Teknis (Known Limitations)

1. **Keamanan Perangkat Keras ESP32**: Penghentian darurat pada suhu kritis (70°C) dirancang untuk dieksekusi secara mandiri oleh firmware lokal ESP32, bukan mengandalkan notifikasi web browser.
2. **Kalkulasi Reduksi Massa**: Pengurangan bobot sampah selama 4 jam mencerminkan reduksi massa selama proses pengolahan (termasuk pelepasan uap air dan dekomposisi aktif), berlabel standar: *"Reduksi Massa Selama Proses Pengolahan"*.
3. **Database Scoping**: Seluruh query data telemetri, inventaris, dan persetujuan diverifikasi ganda pada tingkat server (Eloquent Query Scopes & Middleware), tidak hanya mengandalkan penyembunyian elemen antarmuka.
