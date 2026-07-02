<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';

// Ambil statistik
$total_booking = $conn->query("SELECT COUNT(*) as total FROM penyewaan")->fetch_assoc()['total'];
$total_pelanggan = $conn->query("SELECT COUNT(*) as total FROM pelanggan")->fetch_assoc()['total'];
$total_mobil = $conn->query("SELECT COUNT(*) as total FROM mobil")->fetch_assoc()['total'];

// Booking bulan ini
$bulan_ini = date('m');
$tahun_ini = date('Y');
$booking_bulan_ini = $conn->query("SELECT COUNT(*) as total FROM penyewaan WHERE MONTH(created_at) = '$bulan_ini' AND YEAR(created_at) = '$tahun_ini'")->fetch_assoc()['total'];

// 5 Booking terbaru
$query_recent = "SELECT p.id, p.status, p.tgl_mulai, pl.nama as pelanggan, m.nama as mobil 
                 FROM penyewaan p 
                 JOIN pelanggan pl ON p.pelanggan_id = pl.id 
                 JOIN mobil m ON p.mobil_id = m.id 
                 ORDER BY p.created_at DESC LIMIT 5";
$recent_bookings = $conn->query($query_recent);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — RentalMobilKu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="sidebar-logo">
                    <span class="logo-emoji">🚗</span>
                    <span>RentalMobilKu</span>
                </a>
                <div class="sidebar-badge">ADMIN PANEL</div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-label">Main Menu</div>
                <a href="dashboard.php" class="active"><span class="nav-icon">📊</span> Dashboard</a>
                
                <div class="nav-label">Kelola Data</div>
                <a href="penyewaankelola.php"><span class="nav-icon">📝</span> Penyewaan</a>
                <a href="mobilkelola.php"><span class="nav-icon">🚙</span> Mobil</a>
                <a href="pelanggankelola.php"><span class="nav-icon">👥</span> Pelanggan</a>
            </nav>
            <div class="sidebar-footer">
                <a href="../" target="_blank">🌐 Lihat Website</a>
                <a href="logout.php">🚪 Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="hamburger-admin">☰</button>
                    <div>
                        <h2 class="topbar-title">Dashboard</h2>
                        <span class="topbar-breadcrumb">Ringkasan statistik rental</span>
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
                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">📝</div>
                        <div class="stat-info">
                            <h3><?php echo $total_booking; ?></h3>
                            <p>Total Booking</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">📅</div>
                        <div class="stat-info">
                            <h3><?php echo $booking_bulan_ini; ?></h3>
                            <p>Booking Bulan Ini</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon gold">🚙</div>
                        <div class="stat-info">
                            <h3><?php echo $total_mobil; ?></h3>
                            <p>Total Mobil</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon red">👥</div>
                        <div class="stat-info">
                            <h3><?php echo $total_pelanggan; ?></h3>
                            <p>Total Pelanggan</p>
                        </div>
                    </div>
                </div>

                <div class="table-card">
                    <div class="table-header">
                        <h3 class="table-title">Booking Terbaru</h3>
                        <a href="penyewaankelola.php" class="btn btn-outline btn-sm">Lihat Semua</a>
                    </div>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Pelanggan</th>
                                    <th>Mobil</th>
                                    <th>Tgl Sewa</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($recent_bookings->num_rows > 0): ?>
                                    <?php while($row = $recent_bookings->fetch_assoc()): ?>
                                    <tr>
                                        <td class="customer-info">
                                            <span class="name"><?php echo htmlspecialchars($row['pelanggan']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['mobil']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($row['tgl_mulai'])); ?></td>
                                        <td><span class="badge badge-<?php echo strtolower($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" style="text-align:center; padding: 30px;">Belum ada data booking.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Sidebar Mobile Overlay -->
    <div class="sidebar-overlay"></div>

    <script>
        const hamburger = document.querySelector('.hamburger-admin');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');

        hamburger.addEventListener('click', () => {
            sidebar.classList.add('open');
            overlay.classList.add('show');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });
    </script>
</body>
</html>
