<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';

// Hapus pelanggan
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM pelanggan WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: pelanggankelola.php');
    exit;
}

// Ambil data pelanggan beserta total bookingnya
$query = "SELECT pl.*, COUNT(p.id) as total_booking 
          FROM pelanggan pl 
          LEFT JOIN penyewaan p ON pl.id = p.pelanggan_id 
          GROUP BY pl.id 
          ORDER BY pl.created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pelanggan — RentalMobilKu</title>
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
                <a href="mobilkelola.php"><span class="nav-icon">🚙</span> Mobil</a>
                <a href="pelanggankelola.php" class="active"><span class="nav-icon">👥</span> Pelanggan</a>
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
                        <h2 class="topbar-title">Data Pelanggan</h2>
                        <span class="topbar-breadcrumb">Daftar pelanggan yang pernah menyewa</span>
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
                                    <th>Nama Pelanggan</th>
                                    <th>No WhatsApp</th>
                                    <th>Tgl Daftar</th>
                                    <th>Total Booking</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td class="customer-info"><span class="name"><?php echo htmlspecialchars($row['nama']); ?></span></td>
                                        <td><?php echo htmlspecialchars($row['no_hp']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                        <td><span class="badge badge-city"><?php echo $row['total_booking']; ?> kali</span></td>
                                        <td>
                                            <div class="action-group">
                                                <a href="pelanggankelola.php?action=delete&id=<?php echo $row['id']; ?>" class="action-btn danger" onclick="return confirm('Yakin ingin menghapus pelanggan ini? Semua data penyewaannya akan ikut terhapus.')" title="Hapus">🗑️</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="empty-state">Belum ada data pelanggan</td></tr>
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
