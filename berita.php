<?php
require 'config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Berita - AgroVoice</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-logo">AgroVoice</a>
            <div class="nav-links">
                <a href="index.php">Beranda</a>
                <a href="berita.php" style="color: var(--agro-green); opacity: 1;">Berita</a>
                <a href="galeri.php">Galeri</a>
                <?php require_once 'config/nav.php'; render_agro_select($koneksi, $_GET['kategori'] ?? ''); ?>
            </div>
        </div>
    </nav>

    <!-- HEADER -->
    <header style="padding: 120px 20px 40px; text-align: center; background: var(--apple-bg);">
        <h1 style="font-size: clamp(32px, 5vw, 48px); font-weight: 800; letter-spacing: -1px;">Semua <span style="color: var(--agro-green);">Berita.</span></h1>
        <p style="color: var(--text-muted); margin-top: 10px;">Kumpulan informasi terbaru seputar dunia pertanian.</p>
    </header>

    <!-- GRID BERITA -->
    <section class="section-apple white" style="padding-top: 20px;">
        <div class="grid-apple" id="daftar-berita">
            <p style="text-align:center; width:100%;">Memuat berita...</p>
        </div>
    </section>

    <footer>
        <p>&copy; 2026 AgroVoice AI. Desain terinspirasi dari Apple.</p>
    </footer>

    <script>
        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        const urlParams = new URLSearchParams(window.location.search);
        const kategoriFilter = urlParams.get('kategori');

        async function muatBerita() {
            try {
                let url = 'api_berita.php';
                if (kategoriFilter) url += '?kategori=' + encodeURIComponent(kategoriFilter);

                const response = await fetch(url);
                const data = await response.json();
                const container = document.getElementById('daftar-berita');
                container.innerHTML = '';
                
                if(data.length === 0) {
                    container.innerHTML = '<p style="text-align:center; width:100%;">Belum ada berita di kategori ini.</p>';
                    return;
                }

                data.forEach(b => {
                    const gambar = b.gambar ? 'uploads/' + encodeURIComponent(b.gambar) : 'https://via.placeholder.com/400x200?text=Tanpa+Gambar';
                    container.innerHTML += `
                        <a href="berita_detail.php?id=${b.id}" class="card-apple">
                            <img src="${gambar}" alt="${esc(b.judul)}">
                            <span class="kategori-label">${esc(b.kategori_nama || 'Umum')}</span>
                            <h4>${esc(b.judul)}</h4>
                            <p>${esc(b.ringkasan)}</p>
                        </a>
                    `;
                });
            } catch (error) {
                console.error("Gagal memuat berita:", error);
            }
        }
        document.addEventListener('DOMContentLoaded', muatBerita);
    </script>
</body>
</html>