# 🏥 Project: Medical AI Dashboard

Proyek akhir untuk matakuliah Data Science. Sistem ini adalah *dashboard* berbasis web yang mengintegrasikan framework web (Laravel) dengan layanan *Artificial Intelligence* (FastAPI + LLM) untuk menganalisis berbagai jenis citra medis (X-Ray, CT Scan, MRI, ECG, USG, dan Lesi Kulit). Sistem ini juga dilengkapi dengan fitur RAG (*Retrieval-Augmented Generation*) untuk panduan literatur medis.

---

## 🚀 Status Progres Saat Ini
Bagian fundamental (Data Engineering & Repositori) telah diselesaikan:
1. **Setup Repositori & Struktur:** Arsitektur *microservices* (Pemisahan Web & AI) sudah dikonfigurasi.
2. **Dataset Visual:** Sampel data gambar medis telah dikumpulkan, di-*resize*, dan disimpan secara lokal (diabaikan dari Git agar ukuran repo tetap ringan).
3. **Desain Database:** Cetak biru tabel SQL relasional sudah dirancang dan diuji.
4. **Environment AI:** Kerangka server FastAPI dan daftar pustaka (*library*) Python sudah disiapkan.

---

## struktur folder dan hubungan antar folder
Setiap folder memiliki "profesi" dan ruang kerjanya masing-masing. Berikut adalah penjelasan simpelnya:

```text 
1. laravel_dashboard/ (Ruang Resepsionis & Ruang Dokter)
Fungsi: Ini adalah wajah dari rumah sakit Anda. Di sinilah dokter masuk (login), melihat daftar pasien, menekan tombol upload foto rontgen, dan membaca hasil akhir.

Peran: Dia hanya bertugas melayani pengguna dan menyimpan catatan identitas pasien ke MySQL. Dia tidak bisa mendeteksi penyakit. Jika butuh analisis, dia akan "menelepon" spesialis AI.

2. python_ai_service/ (Laboratorium Spesialis AI)
Ini adalah ruang rahasia tempat semua sihir kecerdasan buatan terjadi. Di dalam laboratorium ini, ada beberapa lemari (folder) dengan fungsi khusus:

models/ (Lemari Kacamata Spesialis): Berisi file-file .pt (YOLO). Anggap ini kacamata pintar. Kalau main.py mau menganalisis X-Ray, dia pakai kacamata X-Ray. Kalau mau analisis MRI, dia ganti pakai kacamata MRI.

knowledge_base/ (Rak Buku Asli): Tempat Data Engineer Anda menaruh buku cetak atau PDF resmi dari Kemenkes (SOP, Pedoman Medis).

vector_db/ (Otak Hafalan AI): AI tidak membaca buku langsung. Skrip ingest_data.py akan membaca PDF di knowledge_base, mengubahnya menjadi angka (vektor), lalu menyimpannya di sini (ChromaDB) sebagai "hafalan memori".

prompts/ (Buku Tata Krama): Berisi file JSON. Ini adalah naskah instruksi agar LLM tahu cara berbicara dengan sopan, terstruktur, dan tidak mengarang bebas saat merespons penyakit.

static/ (Ruang Cetak Foto): Saat YOLO menemukan penyakit, OpenCV akan menggambar kotak merah di foto tersebut. Nah, foto hasil coretan itu disimpan di folder ini agar bisa dipajang/dikirim URL-nya ke layar dokter.

main.py (Sang Kepala Lab): Skrip ini adalah bosnya. Dia yang menerima telepon dari Laravel, mengambil gambar, memakai model YOLO, menggambar kotak, mencari referensi ke RAG, lalu menjawab ke Laravel.

3. database_design/ (Arsip Blueprint)
Fungsi: Folder ini tidak ikut "berjalan" saat aplikasi hidup. Isinya cuma file init_schema.sql yang merupakan cetak biru (desain awal) dari tabel-tabel MySQL Anda (seperti tabel users, patients, analysis_history). Berguna jika laptop salah satu teman tim Anda error dan harus install ulang database dari nol.

4. dataset_sample/ (Pasien Manekin / Latihan)
Fungsi: Kumpulan foto-foto penyakit mentah. Ini bukan tempat untuk training model AI. Ini murni dipakai untuk bahan "tes/simulasi" saat kalian mendemokan aplikasi ke dosen, karena memakai data pasien sungguhan itu dilarang keras secara etika.

🔄 Hubungan Antar-Folder (Alur Cerita Sistem)
Agar semakin kebayang, begini cerita bagaimana folder-folder tersebut saling bekerja sama dalam satu kali klik:

Dokter berada di laravel_dashboard dan mengunggah gambar X-Ray pasien bernama Budi.

Laravel membungkus gambar itu dan melemparnya ke python_ai_service/main.py.

main.py menangkap gambar tersebut, lalu mengambil kacamata X-Ray dari folder models/ untuk mendeteksi penyakitnya (ternyata hasilnya: Pneumonia).

main.py mencoret gambar Budi dengan kotak merah, lalu menyimpan gambar tersebut ke dalam lemari static/.

main.py kemudian mencari panduan pengobatan Pneumonia di dalam otak vector_db/, dan mengambil aturan bicara dari folder prompts/.

main.py mengirimkan semua data itu ke API Gemini.

Setelah Gemini membalas dengan teks medis yang rapi, main.py membungkus teks tersebut beserta link gambar dari folder static/, lalu melemparnya kembali ke laravel_dashboard.

Dokter melihat hasilnya di layar, dan Laravel menyimpan catatannya ke MySQL.




## 📂 Struktur Repositori & Penjelasan File

Repositori ini dibagi menjadi dua bagian utama (Web dan AI) agar tidak saling bentrok. Berikut adalah fungsi dari setiap *folder* dan *file*:

```text
medical_AI/
│
├── laravel_dashboard/          # 🌐 FOLDER TIM WEB (Frontend & Backend)
│   └── .gitkeep                # Penanda agar folder kosong ini dilacak Git. Nanti kerangka Laravel akan di-install di sini.
│
├── python_ai_service/          # 🧠 FOLDER TIM AI (Logic LLM & RAG)
│   ├── knowledge_base/         # Tempat menaruh file literatur medis mentah (PDF/TXT) untuk fitur RAG.
│   ├── prompts/                # Tempat menyimpan file Python khusus untuk instruksi Prompt LLM (misal: xray_prompt.py).
│   ├── vector_db/              # Folder penyimpanan otomatis database vektor (ChromaDB).
│   ├── ingest_data.py          # Skrip untuk mengubah dokumen dari knowledge_base menjadi vektor.
│   ├── main.py                 # File utama Server FastAPI. Bertugas menerima gambar dari Laravel dan menembak API LLM.
│   ├── requirements.txt        # Daftar library Python yang dibutuhkan (FastAPI, OpenAI, LangChain, dll).
│   └── .env.example            # Template file kunci rahasia (API Key). JANGAN masukkan API Key asli ke file ini.
│
├── database_design/            # 🗄️ FOLDER DOKUMENTASI DATA
│   └── init_schema.sql         # Cetak biru (Blueprint) struktur tabel database yang siap diterjemahkan ke Migration Laravel.
│
├── dataset_sample/             # 📊 GUDANG DATA LOKAL (Tidak dilacak Git)
│   ├── sample_ct/              # Sampel gambar CT Scan Otak
│   ├── sample_ecg/             # Sampel gambar sinyal EKG
│   ├── sample_mri/             # Sampel gambar MRI Tumor
│   ├── sample_skin/            # Sampel gambar Lesi Kulit
│   ├── sample_usg/             # Sampel gambar USG Janin
│   └── sample_xray/            # Sampel gambar X-Ray Paru-paru
│
├── .gitignore                  # Aturan untuk mengabaikan file tertentu (seperti dataset besar & file .env) agar tidak masuk ke GitHub.
└── README.md                   # File dokumentasi proyek yang sedang Anda baca ini.


## Pembagian Tugas & Langkah Selanjutnya

silakan perhatikan fokus tugas berikut:

### 1. Tim Web (Frontend & Backend Laravel)
* **Inisialisasi:** Lakukan instalasi proyek Laravel ke dalam folder `laravel_dashboard/`.
* **Database:** Terjemahkan skema SQL yang ada di `database_design/init_schema.sql` menjadi Migration di Laravel.
* **UI/UX:** Bangun halaman *dashboard* (form upload citra, input teks gejala, dan *dropdown* pemilihan model LLM).
* **Integrasi API:** Buat fungsi HTTP Client di Laravel untuk mengirimkan *payload* gambar dan teks ke server Python (FastAPI).

### 2. AI & Prompt Engineer
* **Setup API Key:** Copy file `python_ai_service/.env.example`, ubah namanya menjadi `.env`, lalu isi dengan API Key LLM yang digunakan.
* **Prompt Engineering:** Racik System Prompt spesifik untuk setiap jenis citra medis di dalam folder `prompts/`.
* **Integrasi LLM:** Lengkapi logika pada `main.py` agar bisa menggabungkan gambar dari Laravel dengan prompt, lalu menembaknya ke OpenAI/Claude/Gemini.

### 3. Data Engineer (Fokus Teks & RAG)
* **Kumpulkan Literatur:** Cari dokumen teks/PDF standar penanganan medis dan letakkan di dalam folder `knowledge_base/`.
* **Eksekusi RAG:** Lengkapi logika pada `ingest_data.py` menggunakan LangChain dan ChromaDB untuk mengubah teks tersebut menjadi Vector Embeddings.

---

## 💻 Cara Menjalankan Service AI (Local Development)

Bagi anggota tim yang ingin menyalakan server AI di laptop masing-masing:

1. Buka terminal dan arahkan ke folder AI:
   ```bash
   cd python_ai_service

2. Instal semua library yang dibutuhkan:
   ```bash
   pip install -r requirements.txt

3. Buat file `.env` berdasarkan `.env.example` dan isi API Key-nya.

4. Jalankan server FastAPI:
```bash
python main.py