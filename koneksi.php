<?php
// ================================================================
// RENTAL MOBIL KU — Koneksi Database
// ================================================================
// PENGATURAN UNTUK LARAGON (LOKAL):
//   Host     : localhost
//   Username : root
//   Password : (kosong)
//   Database : rentalmobil_db
//
// PENGATURAN UNTUK INFINITYFREE:
//   Host     : sql306.infinityfree.com  (cek di panel InfinityFree)
//   Username : if0_xxxxxxx              (dari panel InfinityFree)
//   Password : (password dari panel)
//   Database : if0_xxxxxxx_rentalmobil_db (dari panel InfinityFree)
// ================================================================

$host     = 'localhost';
$username = 'root';
$password = '';
$database = 'rentalmobil_db';

// Buat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset ke utf8mb4
$conn->set_charset("utf8mb4");
?>
