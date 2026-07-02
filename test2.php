<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli('localhost', 'root', '', 'rentalmobil_db');
    $id = 1;
    $nama = 'Test';
    $kategori = 'city';
    $tahun = 2022;
    $transmisi = 'Matic';
    $kapasitas = 4;
    $fitur = 'AC';
    $harga = 250000;
    $gambar = null; // simulate no upload when old data is null
    $status = 'tersedia';
    $is_featured = 0;

    $stmt = $conn->prepare("UPDATE mobil SET nama=?, kategori=?, tahun=?, transmisi=?, kapasitas=?, fitur=?, harga_per_hari=?, gambar=?, status=?, is_featured=? WHERE id=?");
    $stmt->bind_param("ssisisisssi", $nama, $kategori, $tahun, $transmisi, $kapasitas, $fitur, $harga, $gambar, $status, $is_featured, $id);
    $stmt->execute();
    file_put_contents('test_result.txt', "SUCCESS");
} catch (Exception $e) {
    file_put_contents('test_result.txt', "ERROR: " . $e->getMessage());
}
