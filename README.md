# 🚢 CargoTrace — Maritime Cargo & Logistics Tracking System

<p align="center">
  <strong>Sistem Informasi Pelacakan Kargo Maritim & Manajemen Logistik Berbasis Web</strong><br>
  <em>Light Glassmorphism UI • Navy Accent • Bilingual Support (ID / EN) • Real-Time Weather & Route Tracking</em>
</p>

---

## 📖 1. Latar Belakang & Tujuan Sistem

Dalam operasional logistik maritim lintas pelabuhan, keterlambatan informasi posisi kargo, pencatatan rute manual, serta kurangnya visibilitas cuaca di pelabuhan tujuan sering menghambat koordinasi antara operator logistik dan pelanggan.

**CargoTrace** dirancang untuk menjawab kebutuhan tersebut dengan menyediakan satu platform terpusat yang menghubungkan **Administrator**, **Logistics Operator**, dan **Customer**:
* Mempercepat proses pencatatan dan pemantauan pengiriman kargo antar-pelabuhan dunia.
* Memberikan transparansi status pengiriman (*Created*, *In Transit*, *Delivered*) serta riwayat *tracking event* dan *exception* secara langsung kepada pelanggan.
* Menyediakan informasi prakiraan cuaca pada koordinat pelabuhan tujuan sebagai bahan pertimbangan operasional pelayaran.

---

## 👥 2. Hak Akses & Fitur Peran (Role-Based Access)

Sistem membagi fungsionalitas ke dalam tiga peran utama sesuai alur bisnis logistik:

### 🛡️ Administrator (`/admin/dashboard`)
* **Manajemen Master Lokasi / Pelabuhan (`/admin/locations`)**: Mengelola daftar pelabuhan asal dan tujuan lengkap dengan kota, negara, serta koordinat lintang dan bujur (*latitude* & *longitude*).
* **Manajemen Master Carrier (`/admin/carriers`)**: Mengelola daftar perusahaan pelayaran (*shipping lines*) dan kode armada.
* **Manajemen Pengguna (`/admin/users`)**: Mengatur akun dan peran (*Admin*, *Operator*, *Customer*).
* **Kontrol Penuh Data Pengiriman (`/admin/shipments`)**: Melihat, menambah, memperbarui status/muatan, serta menghapus data pengiriman beserta jejak *tracking* dan *exception*.

### ⚓ Logistics Operator (`/dashboard`)
* **Dashboard Operasional & Filter Interaktif**: Kartu statistik **Total Pengiriman**, **Dalam Perjalanan (*In Transit*)**, dan **Selesai / Tiba (*Delivered*)** yang dapat diklik langsung untuk menyaring daftar pengiriman di tabel.
* **Pembuatan & Pembaruan Shipment (`/shipments/create`, `/shipments`)**: Menginput nomor resi, memilih pelanggan, carrier, pelabuhan asal/tujuan, jenis kargo, berat, volume, serta estimasi keberangkatan dan kedatangan (*ETA*).
* **Pencatatan Tracking Event & Exception**: Memperbarui kronologi perjalanan kapal dan menangani kendala operasional selama pengiriman.

### 📦 Customer (`/dashboard`)
* **Quick Shipment Tracking**: Form pelacakan resi cepat berdasarkan nomor pengiriman (`shipment_number`) langsung dari halaman utama maupun dashboard pelanggan.
* **Filter Riwayat Kiriman Pribadi**: Kartu statistik interaktif (*Total Shipments*, *In Transit*, *Delivered*) yang memfilter khusus riwayat pengiriman milik pelanggan yang sedang login.
* **Halaman Detail Pelacakan (`/track/{shipmentNumber}`)**: Menampilkan rute asal ke tujuan, status terkini, lini masa *tracking events*, catatan *exception*, serta informasi kondisi cuaca di pelabuhan tujuan.

---

## ✨ 3. Fitur Unggulan & Teknis

* **Antarmuka Seragam (*Light Glassmorphism & Navy Theme*)**: Seluruh halaman (Landing Page, Login, Dashboard Admin/Operator/Customer, Daftar & Form Shipment) menggunakan desain *translucent card* yang bersih dan responsif berbasis Tailwind CSS.
* **Dukungan Multibahasa Penuh (ID / EN)**: Pengalihan bahasa instan antara Bahasa Indonesia dan Bahasa Inggris melalui sesi (`session('locale')`) di seluruh navigasi, banner, kartu statistik, dan tabel.
* **Penyaringan Status Fleksibel**: Controller mendukung variasi format penulisan status (`In Transit` / `IN_TRANSIT`, `Delivered` / `DELIVERED`) sehingga filter dari kartu dashboard selalu akurat.
* **Integrasi Layanan Cuaca (`WeatherService`)**: Mengambil data cuaca berdasarkan koordinat `latitude` dan `longitude` lokasi pelabuhan tujuan pada halaman pelacakan resi.
* **Automated Database Seeder (`DatabaseSeeder.php`)**: Mengisi otomatis akun bawaan, 10 pelabuhan utama dunia & Indonesia, 8 perusahaan pelayaran global, serta data contoh pengiriman.

---

## 🗄️ 4. Struktur Entitas Database Utama

| Tabel / Model | Keterangan & Relasi Utama |
| :--- | :--- |
| **`users`** | Menyimpan data pengguna (`name`, `email`, `password`, `role`: `admin`, `operator`, `customer`). |
| **`locations`** | Master pelabuhan dunia (`name`, `city`, `country`, `latitude`, `longitude`). |
| **`carriers`** | Master perusahaan pelayaran (`name`, `code`). |
| **`shipments`** | Data pengiriman utama (`shipment_number`, `user_id`, `carrier_id`, `origin_location_id`, `destination_location_id`, `cargo_type`, `weight`, `volume`, `expected_departure_date`, `expected_arrival_date`, `status`). |
| **`tracking_events`** | Riwayat pergerakan/status tiap pengiriman (`shipment_id`, catatan lokasi/waktu, pembuat event). |
| **`exceptions`** | Catatan kendala atau keterlambatan operasional pada pengiriman terkait. |

---

## 🛠️ 5. Teknologi yang Digunakan

* **Backend:** PHP 8.x & Laravel Framework
* **Database:** MySQL / MariaDB (`cargotrace_db` via XAMPP)
* **Frontend:** Blade Templating Engine & Tailwind CSS (CDN)
* **External Service:** `WeatherService` (Koordinat Pelabuhan Tujuan)
* **Version Control:** Git & GitHub (`nazwazahra210208-afk/cargotrace`)

---

## ⚙️ 6. Panduan Instalasi & Menjalankan Project

1. **Clone Repository**
   ```bash
   git clone [https://github.com/nazwazahra210208-afk/cargotrace.git](https://github.com/nazwazahra210208-afk/cargotrace.git)
   cd cargotrace
   ```

2. **Install Dependensi PHP**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**
   Salin file `.env.example` menjadi `.env`, lalu sesuaikan pengaturan database MySQL (pastikan Apache & MySQL di XAMPP sudah berjalan):
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cargotrace_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Migrasi Database & Seeding Data Awal**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Jalankan Server Lokal**
   ```bash
   php artisan serve
   ```
   Buka browser dan akses `http://127.0.0.1:8000`.

---

## 🔐 7. Akun Uji Coba (Hasil Database Seeder)

Seluruh akun bawaan menggunakan password yang sama: **`password123`**

| Peran (Role) | Nama Akun | Email Login |
| :--- | :--- | :--- |
| **Admin** | System Administrator | `admin@cargotrace.com` |
| **Operator** | Logistics Operator | `operator@cargotrace.com` |
| **Customer** | Luxury Customer | `customer@cargotrace.com` |
| **Customer** | PT Maju Sejahtera Abadi | `maju@cargotrace.com` |
| **Customer** | Global Maritime Express | `global@cargotrace.com` |
| **Customer** | Nusantara Trading Co. | `nusantara@cargotrace.com` |