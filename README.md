<div align="center">

  <img width="180" height="214" alt="made by me" src="https://github.com/user-attachments/assets/151ccfcc-1fab-454f-8d0c-73b8e7ef4f0d" />
  
  <h1>Secure Point of Sale with Digital Signature & XML Integrity Verification</h1>
  
  [![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
  [![Tailwind](https://img.shields.io/badge/Tailwind_v4-06B6D4?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)
  [![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker)](https://www.docker.com/)
  [![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php)](https://php.net)

  <p class="description">
    Sistem kasir modern yang mengedepankan keamanan integritas data finansial. Menggunakan mekanisme <b>Digital Signature</b> pada snapshot XML untuk mendeteksi manipulasi database relasional secara instan.
  </p>

  [Fitur Utama](#-fitur-unggulan) •
  [Arsitektur](#-penerapan-materi-apl) •
  [Teknologi](#-tech-stack) •
  [Instalasi](#-cara-instalasi-docker) •
  [Keamanan](#-mekanisme-keamanan)

</div>

---
## 📖 Tentang Proyek

**Smart POS** bukan sekadar aplikasi transaksi biasa. Proyek ini dibangun sebagai implementasi nyata dari mata kuliah **Arsitektur Perangkat Lunak (APL)**. Fokus utamanya adalah bagaimana menjamin bahwa data transaksi di database relasional tidak dimodifikasi secara ilegal oleh pihak internal maupun eksternal.

Setiap transaksi "disegel" dalam bentuk file XML yang di-hash menggunakan algoritma **SHA-256**, menciptakan jejak audit yang mustahil dipalsukan tanpa merusak tanda tangan digitalnya.

## 🚀 Fitur Unggulan

### 🛡️ 1. XML Audit Trail System
Setiap kali transaksi berhasil, sistem secara otomatis melakukan *snapshot* data ke format XML.
* **Immutability:** Data di-hash untuk memastikan bukti transaksi tetap asli.
* **Digital Evidence:** File XML berfungsi sebagai bukti sah jika terjadi sengketa data di database utama.

### 🏗️ 2. Service-Repository Pattern
Arsitektur yang rapi dan terukur:
* **Decoupling:** Logika bisnis (Services) terpisah sepenuhnya dari akses data (Repositories).
* **Maintainability:** Memudahkan unit testing dan perubahan database driver di masa depan.

### 🎨 3. Modern Dark Dashboard
Antarmuka kasir yang intuitif dan nyaman di mata:
* Dibangun dengan **Tailwind CSS v4** (versi terbaru).
* Responsif dan dioptimalkan untuk performa tinggi menggunakan **Vite**.

### 🔍 4. Automated Verification
Fitur unggah file XML untuk memverifikasi apakah data transaksi masih asli atau sudah dimodifikasi (manipulasi nilai, tanggal, atau item).

---

## 🏗️ Penerapan Materi APL

Sesuai instruksi tugas, proyek ini mengimplementasikan poin-poin arsitektur berikut:

* **Logic Layer:** Seluruh kalkulasi PPN, stok, dan hashing berada di `Services/`.
* **Data Layer:** Abstraksi akses database menggunakan `Repositories/` dengan driver **PDO/Eloquent**.
* **Data Interchange:** Menggunakan **XML** sebagai format pertukaran data untuk audit trail.
* **Database Security:** Proteksi penuh terhadap *SQL Injection* dan normalisasi database (Foreign Key Constraints).

---

## 🛠 Tech Stack

| Layer | Teknologi |
| :--- | :--- |
| **Backend Framework** | Laravel 12 (PHP 8.4) |
| **Frontend Styling** | Tailwind CSS v4 & Alpine.js |
| **Database** | MySQL 8.4 (Relational) |
| **Infrastructure** | Docker Multi-Container |
| **Architecture** | Service-Repository Pattern |

---

## 🐳 Cara Instalasi (Docker)

Pastikan **Docker Desktop** sudah berjalan di perangkat Anda.

**1. Clone Repository**
```bash
git clone [https://github.com/fathanfuad/smart-pos-xml-audit.git](https://github.com/fathanfuad/smart-pos-xml-audit.git)
cd smart-pos-xml-audit

```

**2. Jalankan Container**

```bash
docker-compose up -d --build

```

**3. Setup Aplikasi**
Gunakan perintah setup otomatis yang telah disediakan:

```bash
docker-compose exec app composer run setup

```

*(Perintah ini akan menjalankan composer install, npm install, key generate, migrate, dan seeding sekaligus)*

**4. Selesai!**

* **Web POS:** `http://localhost:8080/pos`
* **Audit Dashboard:** `http://localhost:8080/report`
* **Database Manager:** `http://localhost:8081` (PHPMyAdmin)

---

## 🛡️ Mekanisme Keamanan

> [!CAUTION]
> **Database Compromised Detection:**
> Jika seseorang mengubah nilai transaksi langsung di database (misal: mengubah total bayar dari 100rb menjadi 10rb), sistem verifikasi akan mendeteksi perbedaan hash antara Database vs XML Snapshot dan memberikan peringatan **"DATA COMPROMISED"**.

---

## 👥 Kontributor (Kelompok 1)

| Nama | Peran |
| --- | --- |
| Fathan Fuad | Lead Architect & Backend Developer |
| Degus Satya | Frontend Developer |
| Gerhan | Database Designer |
| Siti aisyah | QA & Documentation |

---

<div align="center">
Dibuat dengan 🧠 untuk Tugas Besar <b>Arsitektur Perangkat Lunak</b> oleh <b>Kelompok 1</b>
</div>

