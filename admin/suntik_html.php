<?php
// Jalankan SEKALI (buka lewat browser setelah login admin) untuk menyisipkan tombol
// "Kembali ke beranda" ke semua file HTML agro yang sudah ada di uploads/web/.
// File yang sudah punya tombol dilewati, jadi aman dijalankan berulang.
session_start();
if (empty($_SESSION['admin'])) { header("Location: login.php"); exit; }
require '../config/helpers.php';
header("Content-Type: text/plain; charset=utf-8");

$files = array_merge(glob(__DIR__ . '/../uploads/web/*.html') ?: [], glob(__DIR__ . '/../uploads/web/*.htm') ?: []);
if (!$files) { echo "Tidak ada file HTML di uploads/web/.\n"; exit; }

foreach ($files as $f) {
    echo basename($f) . ' : ' . (suntik_tombol_beranda($f) ? "tombol ditambahkan" : "dilewati (sudah ada tombol)") . "\n";
}
echo "\nSelesai.\n";
