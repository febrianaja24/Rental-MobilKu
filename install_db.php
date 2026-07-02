<?php
$conn = new mysqli('localhost', 'root', '');
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Buat database
$conn->query("CREATE DATABASE IF NOT EXISTS rentalmobil_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
$conn->select_db("rentalmobil_db");

// Baca isi file SQL
$sql = file_get_contents(__DIR__ . '/database/rentalmobil.sql');

// Eksekusi semua query di dalam file SQL
if ($conn->multi_query($sql)) {
    do {
        if ($res = $conn->store_result()) {
            $res->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    echo "Sukses";
} else {
    echo "Gagal: " . $conn->error;
}
$conn->close();
?>
