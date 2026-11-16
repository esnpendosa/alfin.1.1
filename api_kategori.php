<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);
require 'config/database.php';
require 'config/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $result = $koneksi->query("SELECT id, nama, slug, file_html FROM kategori ORDER BY nama ASC");
    json_out($result->fetch_all(MYSQLI_ASSOC));
}

require_admin();

if ($method === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    if ($nama === '') json_out(["success" => false, "error" => "Nama kategori wajib diisi"], 400);

    // Slug unik: agri-perikanan, agri-perikanan-2, ...
    $dasar = buat_slug($nama);
    $slug = $dasar;
    $n = 2;
    $cek = $koneksi->prepare("SELECT id FROM kategori WHERE slug = ?");
    while (true) {
        $cek->bind_param("s", $slug);
        $cek->execute();
        if ($cek->get_result()->num_rows === 0) break;
        $slug = $dasar . '-' . $n++;
    }

    try {
        // Halaman HTML kustom disimpan di uploads/web/
        $file_html = simpan_upload('file_html', ['html', 'htm'], 'web', 5);
    } catch (Exception $e) {
        json_out(["success" => false, "error" => $e->getMessage()], 400);
    }
    if ($file_html !== '') {
        // Sisipkan tombol "Kembali ke beranda" ke halaman HTML yang baru diunggah
        suntik_tombol_beranda(__DIR__ . '/uploads/web/' . $file_html);
    }
    $file_html_db = $file_html !== '' ? $file_html : null;

    $stmt = $koneksi->prepare("INSERT INTO kategori (nama, slug, file_html) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama, $slug, $file_html_db);
    if ($stmt->execute()) json_out(["success" => true, "slug" => $slug]);
    json_out(["success" => false, "error" => $stmt->error], 500);
}

if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    $lama = $koneksi->query("SELECT file_html FROM kategori WHERE id = $id")->fetch_assoc();
    $stmt = $koneksi->prepare("DELETE FROM kategori WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        hapus_upload($lama['file_html'] ?? null, 'web');
        json_out(["success" => true]);
    }
    json_out(["success" => false, "error" => $stmt->error], 500);
}

json_out(["success" => false, "error" => "Metode tidak didukung"], 405);
