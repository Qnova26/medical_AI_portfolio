# Medical AI - Clinical Decision Support System 🩺🤖

![Medical AI Banner](https://img.shields.io/badge/AI-Clinical_Decision_Support-blue.svg?style=for-the-badge)
![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![FastAPI](https://img.shields.io/badge/FastAPI-005571?style=for-the-badge&logo=fastapi)
![Python](https://img.shields.io/badge/Python-3776AB?style=for-the-badge&logo=python&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

## 📌 Deskripsi Proyek
**Medical AI** adalah sebuah purwarupa *Clinical Decision Support System* (Sistem Pendukung Keputusan Klinis) berbasis Kecerdasan Buatan (AI) yang dirancang menggunakan arsitektur **Microservices**. Sistem ini membantu tenaga medis (dokter) dalam mendiagnosis penyakit pasien dengan memberikan opini kedua (*second opinion*) yang objektif, cepat, dan berbasis data.

Sistem ini dirancang bukan untuk menggantikan peran dokter, melainkan menjadi "asisten pintar" yang dapat menganalisis dua jenis data kompleks:
1. **Data Klinis:** Keluhan pasien, tanda vital, riwayat medis, dan dokumen hasil laboratorium (termasuk ekstraksi dan konversi otomatis dari PDF).
2. **Data Citra Medis (Radiologi):** Analisis otomatis untuk X-Ray, CT-Scan, MRI, dan EKG—lengkap dengan dukungan pembacaan format mentah rumah sakit **DICOM (`.dcm`)** dan otomatisasi pembuatan anotasi visual (*bounding box* kelainan).

## ✨ Fitur Utama

- **🧠 Multi-modal AI Analysis:** Mampu menganalisis teks klinis dan gambar medis secara bersamaan untuk menghasilkan **Diagnosis Gabungan (Combined Diagnosis)** yang komprehensif.
- **📄 DICOM & PDF Native Support:** Memproses langsung gambar mentah radiologi (`.dcm`) dan dokumen lab (PDF) tanpa mewajibkan dokter mengonversinya secara manual.
- **🎯 Visual Annotations:** AI menyoroti lokasi kelainan/anomali pada gambar radiologi dengan menggambar kotak merah (Bounding Box) secara otomatis menggunakan *OpenCV*.
- **👨‍⚕️ Human-in-the-Loop Validation:** Keputusan akhir tetap berada di tangan dokter. Hasil analisis AI dapat divalidasi, disetujui, atau dikoreksi secara manual melalui sistem.
- **⚙️ Dynamic Prompt Engineering:** Administrator atau dokter dapat mengatur instruksi latar belakang AI (System Prompt) secara dinamis langsung melalui antarmuka *dashboard* tanpa menyentuh *source code*.

---

## 🏗️ Arsitektur & Teknologi

Proyek ini dipisah menjadi dua *service* utama untuk keandalan dan efisiensi:

### 1. Frontend & Main Backend (Laravel 11)
Bertindak sebagai sistem terpusat untuk mengelola antarmuka pengguna, autentikasi, rekam medis pasien, dan visualisasi data.
- **Bahasa & Framework:** PHP 8, Laravel 11
- **Styling:** Tailwind CSS + Vite
- **Database:** MySQL / SQLite via Eloquent ORM

### 2. AI Processing Engine (FastAPI)
Sebuah *microservice* khusus berkinerja tinggi yang ditugaskan untuk memproses operasi berat (*computer vision*, konversi citra) dan menjembatani *request* ke layanan *Large Language Models* (LLM).
- **Bahasa & Framework:** Python 3.10+, FastAPI, Uvicorn
- **AI Integration:** OpenAI API SDK (Multimodal LLM Proxy)
- **Computer Vision & Parsing:** `pydicom` (DICOM Reader), `fitz` / PyMuPDF (PDF Parser), `cv2` (OpenCV), `Pillow`.

---

## 🚀 Panduan Instalasi (Local Development)

Bagi Anda yang ingin menjalankan proyek ini secara lokal, ikuti langkah-langkah di bawah ini:

### A. Persiapan Mesin AI (FastAPI)
1. Buka terminal dan arahkan ke direktori `FastAPI_AI`:
   ```bash
   cd FastAPI_AI
   ```
2. Buat dan aktifkan *Virtual Environment*:
   ```bash
   python -m venv venv
   # Pengguna Windows:
   venv\Scripts\activate
   # Pengguna Linux/Mac:
   source venv/bin/activate
   ```
3. Instal semua dependensi pustaka:
   ```bash
   pip install -r requirements.txt
   ```
4. Buat file `.env` (atau ubah nama dari `.env.example`) dan tambahkan kredensial API Key LLM Anda:
   ```env
   OPENAI_API_KEY=your_api_key_here
   OPENAI_BASE_URL=your_base_url_here
   ```
5. Jalankan *server* FastAPI:
   ```bash
   uvicorn main:app --reload --port 8001
   ```

### B. Persiapan Web Dashboard (Laravel)
1. Buka terminal baru dan arahkan ke direktori `Laravel_Dashboard`:
   ```bash
   cd Laravel_Dashboard
   ```
2. Instal dependensi PHP dan Node.js:
   ```bash
   composer install
   npm install
   ```
3. Siapkan *environment*:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Jalankan migrasi tabel dan masukkan data awal (*seeder*):
   ```bash
   php artisan migrate --seed
   ```
5. Buat tautan simbolis (*symlink*) untuk direktori penyimpanan agar gambar pasien dapat dirender oleh browser:
   ```bash
   php artisan storage:link
   ```
6. Nyalakan layanan pelayan web (Laravel) dan kompilator aset (Vite):
   ```bash
   php artisan serve    # Biasanya berjalan di port 8000
   # Di terminal terpisah:
   npm run dev
   ```

Akses *dashboard* sistem melalui browser di `http://127.0.0.1:8000`.

---

## 🔒 Catatan Keamanan (Portfolio Notice)
- Repositori ini adalah salinan versi rilis untuk tujuan demonstrasi portofolio.
- Segala bentuk Kredensial API, token rahasia, maupun riwayat versi yang memuat privasi (*commit history*) telah dihapus secara permanen.
- Sampel gambar radiologi dan data klinis yang digunakan untuk demonstrasi adalah **100% data anonim (dummy dataset)** dan sama sekali tidak mengandung informasi identitas pasien sungguhan.

---
*Dikembangkan sebagai solusi inovatif integrasi Kecerdasan Buatan dalam alur kerja radiologi dan diagnostik medis.*
