<?php
$conn = new mysqli('localhost', 'root', '', 'rentalmobil');
$result = $conn->query("SELECT id, nama, gambar, emoji, status FROM mobil");
if($result) {
    $out = "";
    while ($row = $result->fetch_assoc()) {
        $out .= json_encode($row) . "\n";
    }
    file_put_contents('db_dump3.txt', $out);
    echo "OK";
} else {
    echo "ERROR: " . $conn->error;
}
