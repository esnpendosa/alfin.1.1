<?php
// Fungsi bantu bersama untuk semua file api_*.php

// Kirim JSON lalu berhenti
function json_out($data, int $status = 200): void {
    if (ob_get_length()) ob_end_clean();
    http_response_code($status);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Wajib login admin untuk aksi tambah / ubah / hapus
function require_admin(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['admin'])) {
        json_out(["success" => false, "error" => "Silakan login sebagai admin dulu."], 401);
    }
}

// Simpan file upload dengan daftar ekstensi yang diizinkan.
// Mengembalikan nama file baru, atau string kosong jika tidak ada file.
// Melempar Exception jika file ditolak.
function simpan_upload(string $field, array $ekstensi_ok, string $subfolder = '', int $maks_mb = 50): string {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return '';
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) throw new Exception("Upload gagal (kode {$f['error']}).");
    if ($f['size'] > $maks_mb * 1024 * 1024) throw new Exception("File terlalu besar (maks {$maks_mb}MB).");

    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $ekstensi_ok, true)) {
        throw new Exception("Jenis file .$ext tidak diizinkan. Boleh: " . implode(', ', $ekstensi_ok));
    }

    $dir = __DIR__ . '/../uploads/' . ($subfolder ? trim($subfolder, '/') . '/' : '');
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $aman = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($f['name']));
    $nama_baru = time() . '_' . $aman;
    if (!move_uploaded_file($f['tmp_name'], $dir . $nama_baru)) throw new Exception("Gagal menyimpan file.");
    return $nama_baru;
}

function hapus_upload(?string $nama, string $subfolder = ''): void {
    if (!$nama) return;
    $path = __DIR__ . '/../uploads/' . ($subfolder ? trim($subfolder, '/') . '/' : '') . basename($nama);
    if (is_file($path)) @unlink($path);
}

// Sisipkan tombol "Kembali ke beranda" ke file HTML agro yang diunggah admin.
// Aman dipanggil berulang: file yang sudah punya tombol tidak diubah lagi.
// Mengembalikan true jika tombol baru saja disisipkan.
function suntik_tombol_beranda(string $path): bool {
    if (!is_file($path)) return false;
    $html = file_get_contents($path);
    if ($html === false || strpos($html, 'data-agrovoice-beranda') !== false) return false;

    // Halaman agro ada di uploads/web/, jadi beranda = ../../index.php
    $tombol = "\n<!-- Tombol dari AgroVoice -->\n"
        . '<a data-agrovoice-beranda href="../../index.php" style="position:fixed;bottom:20px;left:20px;z-index:2147483647;'
        . 'display:inline-block;padding:11px 18px;background:#2e5c36;color:#fff;font:600 14px/1 system-ui,-apple-system,\'Segoe UI\',sans-serif;'
        . 'text-decoration:none;border-radius:999px;box-shadow:0 4px 14px rgba(0,0,0,.28);">&larr; Kembali ke beranda</a>' . "\n";

    $pos = strripos($html, '</body>');
    $html = ($pos !== false) ? substr_replace($html, $tombol, $pos, 0) : $html . $tombol;
    return file_put_contents($path, $html) !== false;
}

function buat_slug(string $teks): string {
    $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $teks), '-'));
    return $slug !== '' ? $slug : 'kategori';
}
