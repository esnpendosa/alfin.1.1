<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);
require 'config/database.php';
require 'config/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];
// Admin mengirim update lewat POST + ?_method=PUT
if ($method === 'POST' && ($_GET['_method'] ?? '') === 'PUT') $method = 'PUT';

$EXT_GAMBAR = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

if ($method === 'GET') {
    // Satu berita (dipakai form edit di admin)
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $koneksi->prepare("SELECT b.*, k.nama AS kategori_nama, COALESCE(b.kategori_id, 0) AS kategori_id
                                   FROM berita b LEFT JOIN kategori k ON b.kategori_id = k.id WHERE b.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) json_out(["success" => false, "error" => "Berita tidak ditemukan"], 404);
        json_out($row);
    }

    // Daftar berita, bisa difilter ?kategori=slug
    $sql = "SELECT b.id, b.judul, b.ringkasan, b.gambar, b.kategori_id, b.tanggal, k.nama AS kategori_nama
            FROM berita b LEFT JOIN kategori k ON b.kategori_id = k.id";
    if (!empty($_GET['kategori'])) {
        $stmt = $koneksi->prepare($sql . " WHERE k.slug = ? ORDER BY b.tanggal DESC, b.id DESC");
        $stmt->bind_param("s", $_GET['kategori']);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $koneksi->query($sql . " ORDER BY b.tanggal DESC, b.id DESC");
    }
    json_out($result->fetch_all(MYSQLI_ASSOC));
}

// Semua aksi di bawah ini hanya untuk admin
require_admin();

if ($method === 'POST' || $method === 'PUT') {
    $judul     = trim($_POST['judul'] ?? '');
    $ringkasan = trim($_POST['ringkasan'] ?? '');
    $isi       = trim($_POST['isi'] ?? '');
    $kategori_id = (isset($_POST['kategori_id']) && $_POST['kategori_id'] !== '0' && $_POST['kategori_id'] !== '')
        ? intval($_POST['kategori_id']) : null; // 0 = Beranda -> NULL

    if ($judul === '' || $ringkasan === '' || $isi === '') {
        json_out(["success" => false, "error" => "Judul, ringkasan, dan isi wajib diisi"], 400);
    }

    try {
        $gambar = simpan_upload('gambar', $EXT_GAMBAR, '', 10);
    } catch (Exception $e) {
        json_out(["success" => false, "error" => $e->getMessage()], 400);
    }

    if ($method === 'POST') {
        $stmt = $koneksi->prepare("INSERT INTO berita (judul, ringkasan, isi, gambar, kategori_id) VALUES (?, ?, ?, ?, ?)");
        $gambar_db = $gambar !== '' ? $gambar : null;
        $stmt->bind_param("ssssi", $judul, $ringkasan, $isi, $gambar_db, $kategori_id);
    } else {
        $id = intval($_POST['id'] ?? 0);
        if ($gambar !== '') {
            // Ganti gambar: hapus gambar lama
            $lama = $koneksi->query("SELECT gambar FROM berita WHERE id = $id")->fetch_assoc();
            hapus_upload($lama['gambar'] ?? null);
            $stmt = $koneksi->prepare("UPDATE berita SET judul=?, ringkasan=?, isi=?, kategori_id=?, gambar=? WHERE id=?");
            $stmt->bind_param("sssisi", $judul, $ringkasan, $isi, $kategori_id, $gambar, $id);
        } else {
            $stmt = $koneksi->prepare("UPDATE berita SET judul=?, ringkasan=?, isi=?, kategori_id=? WHERE id=?");
            $stmt->bind_param("sssii", $judul, $ringkasan, $isi, $kategori_id, $id);
        }
    }

    if ($stmt->execute()) json_out(["success" => true]);
    json_out(["success" => false, "error" => $stmt->error], 500);
}

if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    $lama = $koneksi->query("SELECT gambar FROM berita WHERE id = $id")->fetch_assoc();
    $stmt = $koneksi->prepare("DELETE FROM berita WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        hapus_upload($lama['gambar'] ?? null);
        json_out(["success" => true]);
    }
    json_out(["success" => false, "error" => $stmt->error], 500);
}

json_out(["success" => false, "error" => "Metode tidak didukung"], 405);