-- ================================================================
-- RENTAL MOBIL KU — Database SQL
-- Import file ini ke phpMyAdmin
-- Database: rentalmobil_db
-- ================================================================

-- Buat database (untuk Laragon lokal)
-- Di InfinityFree, database sudah dibuat via panel, jadi bagian ini dihapus agar tidak error

-- ================================================================
-- TABEL: admin
-- ================================================================
CREATE TABLE IF NOT EXISTS `admin` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `nama` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin (password: admin123)
INSERT INTO `admin` (`username`, `password`, `nama`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- ================================================================
-- TABEL: mobil
-- ================================================================
CREATE TABLE IF NOT EXISTS `mobil` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nama` VARCHAR(100) NOT NULL,
    `kategori` ENUM('city', 'mpv', 'suv', 'premium') NOT NULL,
    `tahun` YEAR NOT NULL,
    `transmisi` VARCHAR(50) NOT NULL,
    `kapasitas` INT NOT NULL DEFAULT 4,
    `fitur` TEXT,
    `harga_per_hari` INT NOT NULL,
    `emoji` VARCHAR(10) DEFAULT '🚗',
    `gambar` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('tersedia', 'disewa', 'maintenance') DEFAULT 'tersedia',
    `is_featured` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data mobil sesuai frontend
INSERT INTO `mobil` (`nama`, `kategori`, `tahun`, `transmisi`, `kapasitas`, `fitur`, `harga_per_hari`, `emoji`, `status`, `is_featured`) VALUES
('Honda Brio Satya', 'city', 2022, 'Manual/Matic', 4, 'AC Digital,Bluetooth Audio,Irit BBM', 250000, '🚗', 'tersedia', 0),
('Toyota Yaris GR Sport', 'city', 2023, 'Matic', 5, 'Apple CarPlay,Kamera Mundur,VSC', 300000, '🚗', 'tersedia', 0),
('Toyota Avanza Veloz', 'mpv', 2023, 'Matic', 7, 'Dual AC,Airbag 6,TPMS', 350000, '🚙', 'tersedia', 0),
('Toyota Innova Reborn', 'mpv', 2023, 'Diesel Matic', 7, 'Sunroof,Premium Audio,Full Safety', 450000, '🚙', 'tersedia', 1),
('Toyota Fortuner VRZ', 'suv', 2023, 'Diesel 4×4', 7, '4WD,360° Camera,Head-Up Display', 650000, '🏎️', 'tersedia', 0),
('Toyota Alphard SC', 'premium', 2022, 'Hybrid Matic', 7, 'Ottoman Seat,Captain Seat,Mark Levinson', 1200000, '🚐', 'tersedia', 0);

-- ================================================================
-- TABEL: pelanggan
-- ================================================================
CREATE TABLE IF NOT EXISTS `pelanggan` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nama` VARCHAR(100) NOT NULL,
    `no_hp` VARCHAR(20) NOT NULL,
    `alamat` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================================
-- TABEL: penyewaan
-- ================================================================
CREATE TABLE IF NOT EXISTS `penyewaan` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `pelanggan_id` INT NOT NULL,
    `mobil_id` INT NOT NULL,
    `tgl_mulai` DATE NOT NULL,
    `jam_mulai` TIME NOT NULL,
    `tgl_selesai` DATE NOT NULL,
    `jam_selesai` TIME NOT NULL,
    `opsi_sewa` ENUM('Lepas Kunci', 'Dengan Sopir') NOT NULL DEFAULT 'Lepas Kunci',
    `lokasi_jemput` TEXT DEFAULT NULL,
    `catatan` TEXT DEFAULT NULL,
    `total_harga` INT DEFAULT 0,
    `status` ENUM('pending', 'dikonfirmasi', 'selesai', 'dibatalkan') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`mobil_id`) REFERENCES `mobil`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================================
-- INDEX untuk performa query
-- ================================================================
ALTER TABLE `penyewaan` ADD INDEX `idx_status` (`status`);
ALTER TABLE `penyewaan` ADD INDEX `idx_created` (`created_at`);
ALTER TABLE `pelanggan` ADD INDEX `idx_no_hp` (`no_hp`);
ALTER TABLE `mobil` ADD INDEX `idx_kategori` (`kategori`);
ALTER TABLE `mobil` ADD INDEX `idx_status` (`status`);
