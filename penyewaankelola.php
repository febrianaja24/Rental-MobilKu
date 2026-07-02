<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';

// Proses ubah status
if (isset($_GET['action']) && $_GET['action'] == 'update_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status'];
    $allowed_status = ['pending', 'dikonfirmasi', 'selesai', 'dibatalkan'];
    
    if (in_array($status, $allowed_status)) {
        $stmt = $conn->prepare("UPDATE penyewaan SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
        header('Location: penyewaankelola.php');
        exit;
    }
}

// Ambil data penyewaan
$query = "SELECT p.*, pl.nama as pelanggan, pl.no_hp, m.nama as mobil 
          FROM penyewaan p 
          JOIN pelanggan pl ON p.pelanggan_id = pl.id 
          JOIN mobil m ON p.mobil_id = m.id 
          ORDER BY p.created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Penyewaan — RentalMobilKu</title>
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
                <a href="penyewaankelola.php" class="active"><span class="nav-icon">📝</span> Penyewaan</a>
                <a href="mobilkelola.php"><span class="nav-icon">🚙</span> Mobil</a>
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
                        <h2 class="topbar-title">Data Penyewaan</h2>
                        <span class="topbar-breadcrumb">Kelola semua transaksi booking</span>
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
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Pelanggan</th>
                                    <th>Mobil & Tgl</th>
                                    <th>Opsi Sewa</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td class="customer-info">
                                            <span class="name"><?php echo htmlspecialchars($row['pelanggan']); ?></span>
                                            <span class="phone">📱 <?php echo htmlspecialchars($row['no_hp']); ?></span>
                                        </td>
                                        <td class="customer-info">
                                            <span class="name"><?php echo htmlspecialchars($row['mobil']); ?></span>
                                            <span class="phone">📅 <?php echo date('d/m/Y', strtotime($row['tgl_mulai'])); ?> - <?php echo date('d/m/Y', strtotime($row['tgl_selesai'])); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['opsi_sewa']); ?></td>
                                        <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                        <td><span class="badge badge-<?php echo strtolower($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                        <td>
                                            <select class="status-select" onchange="window.location.href='penyewaankelola.php?action=update_status&id=<?php echo $row['id']; ?>&status='+this.value">
                                                <option value="pending" <?php echo $row['status']=='pending'?'selected':''; ?>>Pending</option>
                                                <option value="dikonfirmasi" <?php echo $row['status']=='dikonfirmasi'?'selected':''; ?>>Dikonfirmasi</option>
                                                <option value="selesai" <?php echo $row['status']=='selesai'?'selected':''; ?>>Selesai</option>
                                                <option value="dibatalkan" <?php echo $row['status']=='dibatalkan'?'selected':''; ?>>Dibatalkan</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="empty-state">Belum ada transaksi penyewaan</td></tr>
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
