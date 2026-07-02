<?php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config/koneksi.php';

    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, nama FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id']       = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_nama']     = $admin['nama'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Password salah.';
            }
        } else {
            $error = 'Username tidak ditemukan.';
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Admin — RentalMobilKu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/admin.css" />
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-logo">
                <span class="logo-emoji">🚗</span>
                <h1>Rental<span>MobilKu</span></h1>
                <p>Panel Admin — Masuk untuk mengelola</p>
            </div>

            <?php if ($error): ?>
                <div class="login-error" style="margin-bottom: 20px;">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form class="login-form" method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan username"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           required autofocus autocomplete="username" />
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password"
                           required autocomplete="current-password" />
                </div>
                <button type="submit" class="btn btn-gold btn-full">
                    🔐 Masuk ke Dashboard
                </button>
            </form>

            <p style="text-align: center; margin-top: 24px; font-size: .82rem; color: #718096;">
                <a href="../" style="color: #2A5298; font-weight: 600;">← Kembali ke Website</a>
            </p>
        </div>
    </div>
</body>
</html>
