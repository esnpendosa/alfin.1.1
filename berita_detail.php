<?php
require 'config/database.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data berita dari database
$stmt = $koneksi->prepare("SELECT b.*, k.nama as kategori_nama FROM berita b LEFT JOIN kategori k ON b.kategori_id = k.id WHERE b.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$berita = $result->fetch_assoc();

// Jika berita tidak ditemukan
if (!$berita) {
    die("<h2 style='text-align:center; margin-top:50px;'>Berita tidak ditemukan. <br><a href='index.php'>Kembali ke Beranda</a></h2>");
}

$gambar = $berita['gambar'] ? 'uploads/' . $berita['gambar'] : 'https://via.placeholder.com/800x400?text=AgroVoice';
$tanggal = date('d F Y', strtotime($berita['tanggal']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $berita['judul'] ?> - AgroVoice</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Bricolage+Grotesque:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #2e5c36; --secondary: #8ebd50; --dark: #1a2416; --bg: #f7f8f4; --card: #fff; --text: #333; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Figtree', sans-serif; background: var(--bg); color: var(--text); line-height: 1.8; }
        a { text-decoration: none; color: inherit; }
        
        /* NAVBAR */
        .navbar { background: rgba(26, 36, 22, 0.95); backdrop-filter: blur(10px); position: fixed; top: 0; left: 0; right: 0; z-index: 1000; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; color: white; }
        .navbar h1 { font-family: 'Bricolage Grotesque', sans-serif; font-size: 24px; color: white; }
        .navbar h1 span { color: var(--secondary); }
        .navbar .menu a { margin-left: 20px; font-weight: 500; font-size: 15px; color: white; }
        .navbar .btn-admin { background: var(--secondary); color: var(--dark); padding: 8px 20px; border-radius: 50px; font-weight: 700; }

        /* ARTIKEL */
        .artikel-container { max-width: 800px; margin: 100px auto 40px auto; padding: 0 20px; }
        .meta-artikel { font-size: 14px; color: #666; margin-bottom: 20px; text-align: center; }
        .meta-artikel span { color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        
        .artikel-container h1 { font-family: 'Bricolage Grotesque', sans-serif; font-size: clamp(28px, 5vw, 42px); color: var(--dark); text-align: center; margin-bottom: 20px; line-height: 1.2; }
        
        .artikel-container img.gambar-utama { width: 100%; max-height: 450px; object-fit: cover; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        
        .isi-artikel { font-size: 18px; color: #444; white-space: pre-wrap; /* Agar enter dari textarea admin terbaca */ }
        
        .btn-kembali { display: inline-block; margin-top: 40px; background: var(--primary); color: white; padding: 10px 20px; border-radius: 8px; font-weight: 600; }
        .btn-kembali:hover { background: var(--dark); }

        /* FOOTER */
        footer { background: var(--dark); color: #a0b8a6; text-align: center; padding: 30px 20px; margin-top: 60px; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php"><h1>Agro<span>Voice</span></h1></a>
        <div class="menu">
            <a href="index.php">Beranda</a>
            <a href="admin/login.php" class="btn-admin">Login Admin</a>
        </div>
    </nav>

    <main class="artikel-container">
        <!-- Kategori & Tanggal -->
        <div class="meta-artikel">
            <span><?= $berita['kategori_nama'] ?? 'Umum' ?></span> &bull; <?= $tanggal ?>
        </div>

        <!-- Judul -->
        <h1><?= $berita['judul'] ?></h1>

        <!-- Gambar Utama -->
        <img src="<?= $gambar ?>" alt="<?= $berita['judul'] ?>" class="gambar-utama">

        <!-- Isi Berita -->
        <div class="isi-artikel">
            <?= htmlspecialchars($berita['isi']) ?>
        </div>

        <!-- Tombol Kembali -->
        <a href="index.php" class="btn-kembali">&larr; Kembali ke Beranda</a>
    </main>

    <footer>
        <p>&copy; 2026 AgroVoice AI. Semua hak dilindungi.</p>
    </footer>

</body>
</html>