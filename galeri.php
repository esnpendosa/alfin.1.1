<?php
require 'config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri - AgroVoice</title>
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
                <a href="berita.php">Berita</a>
                <a href="galeri.php" style="color: var(--agro-green); opacity: 1;">Galeri</a>
                <?php require_once 'config/nav.php'; render_agro_select($koneksi, ''); ?>
            </div>
        </div>
    </nav>

    <!-- HEADER -->
    <header style="padding: 120px 20px 40px; text-align: center; background: var(--apple-bg);">
        <h1 style="font-size: clamp(32px, 5vw, 48px); font-weight: 800; letter-spacing: -1px;">Galeri <span style="color: var(--agro-green);">AgroVoice.</span></h1>
        <p style="color: var(--text-muted); margin-top: 10px;">Dokumentasi foto dan video dari lapangan.</p>
    </header>

    <!-- GRID GALERI -->
    <section class="section-apple white" style="padding-top: 20px;">
        <div class="grid-apple" id="daftar-galeri">
            <p style="text-align:center; width:100%;">Memuat galeri...</p>
        </div>
    </section>

    <footer>
        <p>&copy; 2026 AgroVoice AI. Desain terinspirasi dari Apple.</p>
    </footer>

    <script>
        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        async function muatGaleri() {
            try {
                const response = await fetch('api_galeri.php');
                const data = await response.json();
                const container = document.getElementById('daftar-galeri');
                container.innerHTML = '';
                
                if(data.length === 0) {
                    container.innerHTML = '<p style="text-align:center; width:100%;">Belum ada foto atau video di galeri.</p>';
                    return;
                }

                data.forEach(item => {
                    let media = '';
                    if (item.tipe === 'foto') {
                        media = `<img src="${esc(item.url_file)}" alt="${esc(item.judul)}">`;
                    } else {
                        media = `<video src="${esc(item.url_file)}" controls></video>`;
                    }

                    container.innerHTML += `
                        <div class="card-apple">
                            ${media}
                            <h4 style="text-align:center;">${esc(item.judul || 'Tanpa Judul')}</h4>
                        </div>
                    `;
                });
            } catch (error) {
                console.error("Gagal memuat galeri:", error);
            }
        }
        document.addEventListener('DOMContentLoaded', muatGaleri);
    </script>
</body>
</html>