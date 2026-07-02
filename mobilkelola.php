<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';

// Hapus mobil
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM mobil WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: mobilkelola.php');
    exit;
}

// Ambil data mobil
$query = "SELECT * FROM mobil ORDER BY id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Mobil — RentalMobilKu</title>
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
                        <h2 class="topbar-title">Data Mobil</h2>
                        <span class="topbar-breadcrumb">Kelola armada kendaraan</span>
                    </div>
                </div>
                <div class="topbar-right">
                    <div class="topbar-user">
                        <span>Halo, <?php echo htmlspecialchars($_SESSION['admin_nama']); ?></span>
                        <div class="topbar-avatar"><?php echo strtoupper(substr($_SESSION['admin_nama'], 0, 1)); ?></div>
                    </div>
                </div>
            </header>

            <div class="page-content">
                <div class="table-card">
                    <div class="table-header">
                        <h3 class="table-title">Daftar Armada</h3>
                        <a href="mobiltambah.php" class="btn btn-primary btn-sm">+ Tambah Mobil</a>
                    </div>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kendaraan</th>
                                    <th>Kategori</th>
                                    <th>Harga/Hari</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td class="customer-info" style="display:flex; align-items:center; gap:12px;">
                                            <?php if(!empty($row['gambar'])): ?>
                                                <img src="../assets/images/cars/<?php echo htmlspecialchars($row['gambar']); ?>" alt="car" style="width:50px; height:50px; object-fit:cover; border-radius:6px;">
                                            <?php else: ?>
                                                <div style="width:50px; height:50px; background:#e2e8f0; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:1.5rem;">🚙</div>
                                            <?php endif; ?>
                                            <div>
                                                <span class="name" style="margin-bottom:4px;"><?php echo htmlspecialchars($row['nama']); ?></span>
                                                <span class="phone"><?php echo $row['tahun'] . ' • ' . $row['transmisi']; ?></span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-<?php echo strtolower($row['kategori']); ?>"><?php echo strtoupper($row['kategori']); ?></span></td>
                                        <td>Rp <?php echo number_format($row['harga_per_hari'], 0, ',', '.'); ?></td>
                                        <td><span class="badge badge-<?php echo strtolower($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                        <td>
                                            <div class="action-group">
                                                <a href="mobiledit.php?id=<?php echo $row['id']; ?>" class="action-btn" title="Edit">✏️</a>
                                                <a href="mobilkelola.php?action=delete&id=<?php echo $row['id']; ?>" class="action-btn danger" onclick="return confirm('Yakin ingin menghapus mobil ini?')" title="Hapus">🗑️</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="empty-state">Belum ada data mobil</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div class="sidebar-overlay"></div>
    <script>
        document.querySelector('.hamburger-admin').addEventListener('click', () => {
            document.querySelector('.sidebar').classList.add('open');
            document.querySelector('.sidebar-overlay').classList.add('show');
        });
        document.querySelector('.sidebar-overlay').addEventListener('click', () => {
            document.querySelector('.sidebar').classList.remove('open');
            document.querySelector('.sidebar-overlay').classList.remove('show');
        });
    </script>
</body>
</html>
