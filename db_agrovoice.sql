-- Database AgroVoice. Import lewat phpMyAdmin (tab Import) atau: mysql -u root < db_agrovoice.sql
CREATE DATABASE IF NOT EXISTS agrovoice CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agrovoice;

CREATE TABLE IF NOT EXISTS kategori (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nama      VARCHAR(100) NOT NULL,
    slug      VARCHAR(120) NOT NULL UNIQUE,
    file_html VARCHAR(255) NULL          -- halaman HTML kustom di uploads/web/ (opsional)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS berita (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    judul       VARCHAR(255) NOT NULL,
    ringkasan   VARCHAR(500) NOT NULL,
    isi         TEXT NOT NULL,
    gambar      VARCHAR(255) NULL,
    kategori_id INT NULL,                -- NULL = Beranda / Umum
    tanggal     DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS galeri (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT NULL,
    tipe        ENUM('foto','video') NOT NULL DEFAULT 'foto',
    file        VARCHAR(255) NOT NULL,
    judul       VARCHAR(255) NULL,
    tanggal     DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Data contoh (boleh dihapus lewat panel admin). Pakai file yang sudah ada di folder uploads/.
INSERT INTO kategori (nama, slug, file_html) VALUES
    ('AgriPerikanan', 'agri-perikanan', 'agri-perikanan.html');

INSERT INTO berita (judul, ringkasan, isi, gambar, kategori_id, tanggal) VALUES
    ('Penyuluh dan petani mencatat panen lewat ponsel',
     'Pendampingan di sawah kini lebih ringan: data panen dan jadwal tanam dicatat langsung dari lahan.',
     'Contoh isi berita. Ganti dengan tulisan Anda lewat panel admin.',
     '1794861598_berita test.jpg', NULL, NOW()),
    ('Pakan otomatis membantu pembudidaya ikan',
     'Alat pakan otomatis membuat jadwal beri makan lebih teratur dan mengurangi pakan terbuang.',
     'Contoh isi berita. Ganti dengan tulisan Anda lewat panel admin.',
     '1794872538_agri-perikanan.jpg', 1, NOW() - INTERVAL 1 DAY),
    ('Musim panen: apa yang perlu dicatat petani',
     'Daftar singkat data panen yang berguna untuk menghitung biaya dan menentukan jadwal tanam berikutnya.',
     'Contoh isi berita. Ganti dengan tulisan Anda lewat panel admin.',
     '1794861001_panen.jpg', NULL, NOW() - INTERVAL 2 DAY);

INSERT INTO galeri (kategori_id, tipe, file, judul) VALUES
    (NULL, 'foto', '1794861001_panen.jpg', 'Panen padi'),
    (NULL, 'foto', '1794872361_ayam.jpg', 'Peternakan ayam'),
    (1,    'foto', '1794872538_agri-perikanan.jpg', 'Budidaya ikan');