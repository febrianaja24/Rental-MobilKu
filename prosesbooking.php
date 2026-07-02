<?php
// ================================================================
// RENTAL MOBIL KU — Proses Booking (AJAX endpoint)
// ================================================================
session_start();
header('Content-Type: application/json');

require_once 'config/koneksi.php';

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}

// Ambil data dari POST
$nama       = isset($_POST['nama']) ? trim($_POST['nama']) : '';
$no_hp      = isset($_POST['no_hp']) ? trim($_POST['no_hp']) : '';
$mobil_nama = isset($_POST['mobil_nama']) ? trim($_POST['mobil_nama']) : '';
$mobil_harga = isset($_POST['mobil_harga']) ? intval($_POST['mobil_harga']) : 0;
$tgl_mulai  = isset($_POST['tgl_mulai']) ? trim($_POST['tgl_mulai']) : '';
$jam_mulai  = isset($_POST['jam_mulai']) ? trim($_POST['jam_mulai']) : '';
$tgl_selesai = isset($_POST['tgl_selesai']) ? trim($_POST['tgl_selesai']) : '';
$jam_selesai = isset($_POST['jam_selesai']) ? trim($_POST['jam_selesai']) : '';
$opsi_sewa  = isset($_POST['opsi_sewa']) ? trim($_POST['opsi_sewa']) : 'Lepas Kunci';
$lokasi     = isset($_POST['lokasi']) ? trim($_POST['lokasi']) : '';
$catatan    = isset($_POST['catatan']) ? trim($_POST['catatan']) : '';

// Validasi
if (empty($nama) || empty($no_hp) || empty($tgl_mulai) || empty($jam_mulai) || empty($tgl_selesai) || empty($jam_selesai)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

// Validasi opsi sewa
if (!in_array($opsi_sewa, ['Lepas Kunci', 'Dengan Sopir'])) {
    $opsi_sewa = 'Lepas Kunci';
}

// Cari atau buat pelanggan berdasarkan no_hp
$stmt = $conn->prepare("SELECT id FROM pelanggan WHERE no_hp = ?");
$stmt->bind_param("s", $no_hp);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Pelanggan sudah ada, update nama jika berubah
    $pelanggan = $result->fetch_assoc();
    $pelanggan_id = $pelanggan['id'];

    $stmt_update = $conn->prepare("UPDATE pelanggan SET nama = ?, updated_at = NOW() WHERE id = ?");
    $stmt_update->bind_param("si", $nama, $pelanggan_id);
    $stmt_update->execute();
    $stmt_update->close();
} else {
    // Pelanggan baru
    $stmt_insert = $conn->prepare("INSERT INTO pelanggan (nama, no_hp) VALUES (?, ?)");
    $stmt_insert->bind_param("ss", $nama, $no_hp);
    $stmt_insert->execute();
    $pelanggan_id = $conn->insert_id;
    $stmt_insert->close();
}
$stmt->close();

// Cari mobil_id berdasarkan nama mobil
$stmt_mobil = $conn->prepare("SELECT id FROM mobil WHERE nama = ?");
$stmt_mobil->bind_param("s", $mobil_nama);
$stmt_mobil->execute();
$result_mobil = $stmt_mobil->get_result();

if ($result_mobil->num_rows > 0) {
    $mobil = $result_mobil->fetch_assoc();
    $mobil_id = $mobil['id'];
} else {
    // Mobil tidak ditemukan di database, gunakan ID 1 sebagai fallback
    $mobil_id = 1;
}
$stmt_mobil->close();

// Hitung total harga
$start = new DateTime("$tgl_mulai $jam_mulai");
$end   = new DateTime("$tgl_selesai $jam_selesai");
$diff  = $start->diff($end);
$total_hari = $diff->days;
if ($total_hari < 1) $total_hari = 1;
$total_harga = $mobil_harga * $total_hari;

// Insert penyewaan
$stmt_sewa = $conn->prepare("INSERT INTO penyewaan (pelanggan_id, mobil_id, tgl_mulai, jam_mulai, tgl_selesai, jam_selesai, opsi_sewa, lokasi_jemput, catatan, total_harga, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
$stmt_sewa->bind_param("iisssssssi",
    $pelanggan_id,
    $mobil_id,
    $tgl_mulai,
    $jam_mulai,
    $tgl_selesai,
    $jam_selesai,
    $opsi_sewa,
    $lokasi,
    $catatan,
    $total_harga
);

if ($stmt_sewa->execute()) {
    $booking_id = $conn->insert_id;
    echo json_encode([
        'success' => true,
        'message' => 'Booking berhasil disimpan',
        'booking_id' => $booking_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menyimpan booking: ' . $stmt_sewa->error
    ]);
}

$stmt_sewa->close();
$conn->close();
?>
