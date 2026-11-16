<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);
require 'config/database.php';
require 'config/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $result = $koneksi->query("SELECT * FROM galeri ORDER BY tanggal DESC, id DESC");
    $galeri = [];
    while ($row = $result->fetch_assoc()) {
        $row['url_file'] = 'uploads/' . rawurlencode($row['file']);
        $galeri[] = $row;
    }
    json_out($galeri);
}

require_admin();

if ($method === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    if ($judul === '' || !isset($_POST['kategori_id'])) {
        json_out(["success" => false, "error" => "Data form tidak lengkap"], 400);
    }
    // kategori_id 0 = Beranda -> NULL
    $kategori_id = ($_POST['kategori_id'] === '0' || $_POST['kategori_id'] === '') ? null : intval($_POST['kategori_id']);

    $VIDEO = ['mp4', 'webm', 'ogg'];
    $FOTO  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    try {
        $file = simpan_upload('file', array_merge($VIDEO, $FOTO), '', 50);
    } catch (Exception $e) {
        json_out(["success" => false, "error" => $e->getMessage()], 400);
    }
    if ($file === '') json_out(["success" => false, "error" => "File tidak ditemukan"], 400);

    $tipe = in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $VIDEO, true) ? 'video' : 'foto';
    $stmt = $koneksi->prepare("INSERT INTO galeri (kategori_id, tipe, file, judul) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $kategori_id, $tipe, $file, $judul);
    if ($stmt->execute()) json_out(["success" => true, "pesan" => "Berhasil upload"]);
    json_out(["success" => false, "error" => $stmt->error], 500);
}

json_out(["success" => false, "error" => "Metode tidak didukung"], 405);