<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $kategori = $_POST['kategori'];
    $tahun = $_POST['tahun'];
    $transmisi = trim($_POST['transmisi']);
    $kapasitas = intval($_POST['kapasitas']);
    $fitur = trim($_POST['fitur']);
    $harga = intval($_POST['harga_per_hari']);
    $status = $_POST['status'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    $gambar = '';

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['gambar']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['gambar']['name']);
        $target_dir = '../assets/images/cars/';
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $target_file = $target_dir . $file_name;
        
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($imageFileType, $allowed)) {
            if ($_FILES['gambar']['size'] <= 2000000) { // Max 2MB
                if (move_uploaded_file($tmp_name, $target_file)) {
                    $gambar = $file_name;
                } else {
                    $error = 'Gagal mengupload gambar.';
                }
            } else {
                $error = 'Ukuran gambar maksimal 2MB.';
            }
        } else {
            $error = 'Format gambar harus JPG, PNG, atau WEBP.';
        }
    }

    if (empty($nama) || empty($harga)) {
        $error = 'Nama dan Harga per hari harus diisi!';
    } elseif (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO mobil (nama, kategori, tahun, transmisi, kapasitas, fitur, harga_per_hari, gambar, status, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisisisss", $nama, $kategori, $tahun, $transmisi, $kapasitas, $fitur, $harga, $gambar, $status, $is_featured);
        
        if ($stmt->execute()) {
            $success = 'Mobil berhasil ditambahkan!';
            header("refresh:1;url=mobilkelola.php");
        } else {
            $error = 'Gagal menyimpan: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mobil — RentalMobilKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="sidebar-logo">
                    <span class="logo-emoji">🚗</span><span>RentalMobilKu</span>
                </a>
                <div class="sidebar-badge">ADMIN PANEL</div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-label">Main Menu</div>
                <a href="dashboard.php"><span class="nav-icon">📊</span> Dashboard</a>
                
                <div class="nav-label">Kelola Data</div>
                <a href="penyewaankelola.php"><span class="nav-icon">📝</span> Penyewaan</a>
                <a href="mobilkelola.php" class="active"><span class="nav-icon">🚙</span> Mobil</a>
                <a href="pelanggankelola.php"><span class="nav-icon">👥</span> Pelanggan</a>
            </nav>
            <div class="sidebar-footer">
                <a href="../" target="_blank">🌐 Lihat Website</a>
                <a href="logout.php">🚪 Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="hamburger-admin">☰</button>
                    <div>
                        <h2 class="topbar-title">Tambah Mobil Baru</h2>
                        <span class="topbar-breadcrumb">Tambah data armada kendaraan</span>
                    </div>
                </div>
            </header>

            <div class="page-content">
                <div class="form-card">
                    <?php if ($error): ?><div class="alert alert-error">⚠️ <?php echo $error; ?></div><?php endif; ?>
                    <?php if ($success): ?><div class="alert alert-success">✅ <?php echo $success; ?></div><?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nama Kendaraan *</label>
                                <input type="text" name="nama" required placeholder="Cth: Toyota Avanza Veloz">
                            </div>
                            <div class="form-group">
                                <label>Kategori *</label>
                                <select name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="city">City Car</option>
                                    <option value="mpv">MPV</option>
                                    <option value="suv">SUV</option>
                                    <option value="premium">Premium</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row" style="margin-top:16px">
                            <div class="form-group">
                                <label>Tahun *</label>
                                <input type="number" name="tahun" required value="<?php echo date('Y'); ?>">
                            </div>
                            <div class="form-group">
                                <label>Transmisi *</label>
                                <input type="text" name="transmisi" required placeholder="Cth: Matic">
                            </div>
                        </div>

                        <div class="form-row" style="margin-top:16px">
                            <div class="form-group">
                                <label>Kapasitas Penumpang *</label>
                                <input type="number" name="kapasitas" required value="7">
                            </div>
                            <div class="form-group">
                                <label>Harga per Hari (Rp) *</label>
                                <input type="number" name="harga_per_hari" required placeholder="Cth: 350000">
                            </div>
                        </div>

                        <div class="form-group" style="margin-top:16px">
                            <label>Fitur (Pisahkan dengan koma)</label>
                            <input type="text" name="fitur" placeholder="Cth: Dual AC,Airbag 6,TPMS">
                        </div>

                        <div class="form-row" style="margin-top:16px">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="tersedia">Tersedia</option>
                                    <option value="disewa">Disewa</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Foto Kendaraan</label>
                                <input type="file" name="gambar" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-top:16px; flex-direction:row; align-items:center; gap:10px;">
                            <input type="checkbox" name="is_featured" id="is_featured" value="1">
                            <label for="is_featured" style="cursor:pointer">Tandai sebagai Mobil Terlaris / Unggulan</label>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Simpan Mobil</button>
                            <a href="mobilkelola.php" class="btn btn-outline">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
