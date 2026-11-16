<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
require '../config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - AgroVoice</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; background: #2e5c36; color: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; }
        .header a { color: #ffcccc; text-decoration: none; font-weight: bold; }
        
        .tabs { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .tab-btn { background: #ddd; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; color: #333; font-size: 14px; }
        .tab-btn.active { background: #2e5c36; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        input, textarea, select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        button[type="submit"] { background: #2e5c36; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; margin-top: 10px; }
        button[type="submit"]:hover { background: #1a2416; }
        
        .list-item { border-bottom: 1px solid #eee; padding: 10px 0; display: flex; justify-content: space-between; align-items: center; }
        .list-item:last-child { border-bottom: none; }
        .btn-hapus { background: #d9534f; padding: 5px 10px; font-size: 12px; margin: 0; color: white; border: none; border-radius: 3px; cursor: pointer; }
        .btn-edit { background: #f0ad4e; padding: 5px 10px; font-size: 12px; margin: 0 5px; color: white; border: none; border-radius: 3px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Panel Admin AgroVoice</h2>
            <a href="logout.php">Logout</a>
        </div>

        <div class="tabs">
            <button class="tab-btn active" onclick="bukaTab('tab-berita', this)">📝 Kelola Berita</button>
            <button class="tab-btn" onclick="bukaTab('tab-kategori', this)">🌐 Kelola Agro</button>
            <button class="tab-btn" onclick="bukaTab('tab-galeri', this)">🖼️ Kelola Galeri</button>
        </div>

        <!-- TAB 1: BERITA -->
        <div id="tab-berita" class="tab-content active">
            <div class="card">
                <h3 id="form-title">Tambah Berita Baru</h3>
                <form id="form-berita" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="berita-id" value="">
                    <label>Judul Berita</label>
                    <input type="text" name="judul" id="berita-judul" required>
                    <label>Ringkasan</label>
                    <input type="text" name="ringkasan" id="berita-ringkasan" required>
                    <label>Agro</label>
                    <select name="kategori_id" id="berita-kategori" required>
                        <option value="">-- Pilih Agro --</option>
                        <option value="0">Beranda (AgroVoice)</option>
                        <?php
                        $res = $koneksi->query("SELECT * FROM kategori");
                        while ($row = $res->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['nama']}</option>";
                        }
                        ?>
                    </select>
                    <label>Isi Berita</label>
                    <textarea name="isi" id="berita-isi" rows="5" required></textarea>
                    <label>Gambar Utama (Kosongkan jika tidak ingin ganti)</label>
                    <input type="file" name="gambar" accept="image/*">
                    <button type="submit" id="btn-submit">Simpan Berita</button>
                    <button type="button" id="btn-batal" style="background: #666; display: none;" onclick="resetForm()">Batal Edit</button>
                    <p id="status" style="font-weight: bold;"></p>
                </form>
            </div>
            <div class="card">
                <h3>Daftar Berita</h3>
                <div id="daftar-berita">Memuat data...</div>
            </div>
        </div>

        <!-- TAB 2: KATEGORI -->
        <div id="tab-kategori" class="tab-content">
            <div class="card">
                <h3>Tambah Agro Baru</h3>
                <form id="form-kategori" enctype="multipart/form-data">
                    <label>Nama Agro</label>
                    <input type="text" name="nama" required placeholder="Contoh: AgriPerikanan">
                    
                    <label>Upload File HTML (Opsional)</label>
                    <input type="file" name="file_html" accept=".html">
                    <small style="color: #666; display: block; margin-bottom: 10px;">Jika diunggah, halaman ini akan menggunakan desain HTML Anda sendiri.</small>

                    <button type="submit">Tambah Agro</button>
                    <p id="status-kategori" style="font-weight: bold;"></p>
                </form>
            </div>
            <div class="card">
                <h3>Daftar Agro Aktif</h3>
                <div id="daftar-kategori">Memuat data...</div>
            </div>
        </div>

        <!-- TAB 3: GALERI -->
        <div id="tab-galeri" class="tab-content">
            <div class="card">
                <h3>Upload Galeri (Foto/Video)</h3>
                <form id="form-galeri" enctype="multipart/form-data">
                    <label>Judul Foto/Video</label>
                    <input type="text" name="judul" required>
                    
                    <label>Pilih Agro</label>
                    <select name="kategori_id" id="galeri-kategori" required>
                        <option value="">-- Pilih Agro --</option>
                        <option value="0">Beranda (AgroVoice)</option>
                        <?php
                        $res2 = $koneksi->query("SELECT * FROM kategori");
                        while ($row2 = $res2->fetch_assoc()) {
                            echo "<option value='{$row2['id']}'>{$row2['nama']}</option>";
                        }
                        ?>
                    </select>

                    <label>File (Gambar/Video Maks 50MB)</label>
                    <input type="file" name="file" accept="image/*,video/*" required>

                    <button type="submit">Upload Galeri</button>
                    <p id="status-galeri" style="font-weight: bold;"></p>
                </form>
            </div>
        </div>

    </div>

    <script>
        // Otomatis pilih Beranda saat halaman dimuat (jika belum ada pilihan)
        document.addEventListener('DOMContentLoaded', function() {
            const selBerita = document.getElementById('berita-kategori');
            const selGaleri = document.getElementById('galeri-kategori');
            if (selBerita && selBerita.value === '') selBerita.value = '0';
            if (selGaleri && selGaleri.value === '') selGaleri.value = '0';
        });

        function bukaTab(tabId, btn) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            btn.classList.add('active');
        }

        // --- LOGIKA BERITA ---
        document.getElementById('form-berita').onsubmit = async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const status = document.getElementById('status');
            const isEdit = document.getElementById('berita-id').value !== "";
            status.innerText = "Memproses..."; status.style.color = "blue";
            try {
                let url = isEdit ? '../api_berita.php?_method=PUT' : '../api_berita.php';
                const res = await fetch(url, { method: 'POST', body: formData });
                const result = await res.json();
                if (result.success) {
                    status.innerText = isEdit ? "Berita diubah!" : "Berita disimpan!";
                    status.style.color = "green"; resetForm(); muatBerita();
                } else { status.innerText = "Gagal: " + result.error; status.style.color = "red"; }
            } catch (err) { status.innerText = "Error sistem."; status.style.color = "red"; }
        };

        async function muatBerita() {
            const res = await fetch('../api_berita.php');
            const data = await res.json();
            const container = document.getElementById('daftar-berita');
            container.innerHTML = data.length === 0 ? '<p>Belum ada berita.</p>' : '';
            data.forEach(b => {
                container.innerHTML += `
                    <div class="list-item">
                        <div><strong>${b.judul}</strong><br><small>${b.ringkasan}</small></div>
                        <div>
                            <button class="btn-edit" onclick="editBerita(${b.id})">Edit</button>
                            <button class="btn-hapus" onclick="hapusBerita(${b.id})">Hapus</button>
                        </div>
                    </div>`;
            });
        }

        async function hapusBerita(id) {
            if(confirm('Yakin hapus berita ini?')) {
                const res = await fetch(`../api_berita.php?id=${id}`, { method: 'DELETE' });
                const result = await res.json();
                if (result.success) { alert('Terhapus!'); muatBerita(); } 
                else { alert('Gagal: ' + result.error); }
            }
        }

        async function editBerita(id) {
            const res = await fetch(`../api_berita.php?id=${id}`);
            const data = await res.json();
            document.getElementById('berita-id').value = data.id;
            document.getElementById('berita-judul').value = data.judul;
            document.getElementById('berita-ringkasan').value = data.ringkasan;
            document.getElementById('berita-isi').value = data.isi;
            document.getElementById('berita-kategori').value = data.kategori_id ? data.kategori_id : '0';
            document.getElementById('form-title').innerText = "Edit Berita";
            document.getElementById('btn-submit').innerText = "Update Berita";
            document.getElementById('btn-batal').style.display = "inline-block";
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function resetForm() {
            document.getElementById('form-berita').reset();
            document.getElementById('berita-id').value = "";
            document.getElementById('form-title').innerText = "Tambah Berita Baru";
            document.getElementById('btn-submit').innerText = "Simpan Berita";
            document.getElementById('btn-batal').style.display = "none";
            document.getElementById('status').innerText = "";
            // Setelah reset, kembalikan dropdown ke Beranda
            document.getElementById('berita-kategori').value = '0';
        }

        // --- LOGIKA KATEGORI ---
        document.getElementById('form-kategori').onsubmit = async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const status = document.getElementById('status-kategori');
            status.innerText = "Menyimpan...";
            try {
                const res = await fetch('../api_kategori.php', { method: 'POST', body: formData });
                const result = await res.json();
                if (result.success) {
                    status.innerText = "Agro berhasil ditambahkan! Sudah muncul di pilihan Agro.";
                    status.style.color = "green"; this.reset(); muatKategori();
                } else { status.innerText = "Gagal: " + result.error; status.style.color = "red"; }
            } catch (err) { status.innerText = "Error sistem."; status.style.color = "red"; }
        };

        function isiPilihanAgro(daftar) {
            ['berita-kategori', 'galeri-kategori'].forEach(id => {
                const sel = document.getElementById(id);
                if (!sel) return;
                const nilaiLama = sel.value;
                sel.innerHTML = '';
                [['', '-- Pilih Agro --'], ['0', 'Beranda (AgroVoice)']].concat(daftar.map(k => [String(k.id), k.nama]))
                    .forEach(([v, t]) => { const o = document.createElement('option'); o.value = v; o.textContent = t; sel.appendChild(o); });
                sel.value = nilaiLama;
            });
        }

        async function muatKategori() {
            const res = await fetch('../api_kategori.php');
            const data = await res.json();
            isiPilihanAgro(data);
            const container = document.getElementById('daftar-kategori');
            container.innerHTML = data.length === 0 ? '<p>Belum ada agro.</p>' : '';
            data.forEach(k => {
                let infoFile = k.file_html ? `<span style="color: green; font-size:12px;">✓ Custom HTML</span>` : `<span style="color: gray; font-size:12px;">Template Bawaan</span>`;
                container.innerHTML += `
                    <div class="list-item">
                        <div>
                            <strong>${k.nama}</strong><br>
                            <small>URL: /${k.slug}</small><br>${infoFile}
                        </div>
                        <button class="btn-hapus" onclick="hapusKategori(${k.id})">Hapus</button>
                    </div>`;
            });
        }

        async function hapusKategori(id) {
            if(confirm('Yakin hapus agro ini?')) {
                const res = await fetch(`../api_kategori.php?id=${id}`, { method: 'DELETE' });
                const result = await res.json();
                if (result.success) { alert('Terhapus!'); muatKategori(); } 
                else { alert('Gagal: ' + result.error); }
            }
        }

        // --- LOGIKA GALERI ---
        document.getElementById('form-galeri').onsubmit = async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const status = document.getElementById('status-galeri');
            status.innerText = "Mengunggah... (Mohon tunggu jika video besar)";
            status.style.color = "blue";

            try {
                const res = await fetch('../api_galeri.php', { method: 'POST', body: formData });
                const result = await res.json();
                
                if (result.success) {
                    status.innerText = "Galeri berhasil di-upload!";
                    status.style.color = "green";
                    this.reset();
                    // Setelah reset, kembalikan dropdown ke Beranda
                    document.getElementById('galeri-kategori').value = '0';
                } else {
                    status.innerText = "Gagal: " + result.error;
                    status.style.color = "red";
                }
            } catch (err) {
                status.innerText = "Terjadi kesalahan sistem.";
                status.style.color = "red";
            }
        };

        muatBerita();
        muatKategori();
    </script>
</body>
</html>