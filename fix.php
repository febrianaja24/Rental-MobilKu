<?php
// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'rentalmobil_db');

// Buat hash baru untuk "admin123"
$hash = password_hash('admin123', PASSWORD_DEFAULT);

// Update ke database
$conn->query("UPDATE admin SET password = '$hash' WHERE username = 'admin'");

echo "Sukses! Password admin telah direset menjadi: <b>admin123</b><br><br>";
echo "<a href='admin/login.php'>Klik di sini untuk login</a>";
?>
