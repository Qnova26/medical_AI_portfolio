-- ==============================================================
-- CETAK BIRU DATABASE: CAPSTONE MEDICAL AI
-- ==============================================================

-- 1. Tabel User
CREATE TABLE user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama_user VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    kata_sandi VARCHAR(255),
    no_telp_pasien VARCHAR(20)
);

-- 2. Tabel Pasien
CREATE TABLE pasien (
    id_pasien INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    no_rm VARCHAR(50),
    nama_pasien VARCHAR(255),
    no_telp_pasien VARCHAR(20),
    tanggal_lahir_pasien DATE,
    jenis_kelamin_pasien ENUM('L', 'P'),
    alamat_pasien TEXT,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE SET NULL
);

-- 3. Tabel File Medis
CREATE TABLE file_medis (
    id_file_medis INT AUTO_INCREMENT PRIMARY KEY,
    id_pasien INT,
    nama_file VARCHAR(255),
    tipe_file VARCHAR(50),
    path_file TEXT,

    FOREIGN KEY (id_pasien) REFERENCES pasien(id_pasien) ON DELETE CASCADE
);

-- 4. Tabel Prompt Template
CREATE TABLE prompt_template (
    id_prompt INT AUTO_INCREMENT PRIMARY KEY,
    nama_prompt VARCHAR(255),
    kategori VARCHAR(100),
    level VARCHAR(50),
    sistem_prompt TEXT,
    user_prompt TEXT
);

-- 5. Tabel Analisis
CREATE TABLE analisis (
    id_analisis INT AUTO_INCREMENT PRIMARY KEY,
    id_pasien INT,
    id_prompt INT,
    tanda_vital TEXT,
    gejala_klinis TEXT,
    alergi TEXT,
    diagnosis TEXT,
    rekomendasi TEXT,
    tingkat_keyakinan FLOAT,
    status VARCHAR(50),
    level_hasil VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_pasien) REFERENCES pasien(id_pasien) ON DELETE CASCADE,
    FOREIGN KEY (id_prompt) REFERENCES prompt_template(id_prompt) ON DELETE SET NULL
);

-- 6. Tabel Analisis File (Relasi Many-to-Many)
CREATE TABLE analisis_file (
    id_analisis INT,
    id_file INT,

    PRIMARY KEY (id_analisis, id_file),

    FOREIGN KEY (id_analisis) REFERENCES analisis(id_analisis) ON DELETE CASCADE,
    FOREIGN KEY (id_file) REFERENCES file_medis(id_file_medis) ON DELETE CASCADE
);
