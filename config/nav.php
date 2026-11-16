<?php
// Dropdown "Agro" di menu atas. Dipakai di index.php, berita.php, dan galeri.php.
// Urutan: AgroVoice (beranda) selalu pertama, lalu setiap agro yang ditambahkan admin.
function render_agro_select($koneksi, $slug_aktif = '') {
    echo '<div class="nav-dropdown"><select aria-label="Pilih agro" onchange="if(this.value) window.location.href=this.value">';
    echo '<option value="index.php"' . ($slug_aktif === '' ? ' selected' : '') . '>AgroVoice</option>';

    $hasil = $koneksi->query("SELECT nama, slug, file_html FROM kategori ORDER BY nama");
    while ($k = $hasil->fetch_assoc()) {
        // Agro dengan halaman HTML sendiri -> buka halaman itu; selain itu -> daftar berita agro tsb
        $href = !empty($k['file_html'])
            ? 'uploads/web/' . rawurlencode($k['file_html'])
            : 'berita.php?kategori=' . urlencode($k['slug']);
        $sel = ($slug_aktif !== '' && $slug_aktif === $k['slug']) ? ' selected' : '';
        echo '<option value="' . htmlspecialchars($href, ENT_QUOTES) . '"' . $sel . '>'
           . htmlspecialchars($k['nama']) . '</option>';
    }
    echo '</select></div>';
}
