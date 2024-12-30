<?php
session_start();

// Cek apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Koneksi ke database
$host = "localhost";
$user = "root";
$password = "";
$database = "salon_database";
$conn = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil data admin berdasarkan id_admin
$query = "SELECT * FROM akun_admin WHERE id_admin = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['admin_id']); // Menggunakan "s" karena id_admin adalah string
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah query berhasil dan ada data admin
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
} else {
    // Menampilkan pesan error jika data admin tidak ditemukan
    $error_message = "Admin tidak ditemukan. Pastikan Anda sudah login.";
}

// Proses pembaruan data admin jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form dan validasi
    $nama_admin = htmlspecialchars($_POST['nama_admin'] ?? ''); // Kolom nama_admin
    $alamat = htmlspecialchars($_POST['alamat'] ?? ''); // Kolom alamat
    $no_hp = $_POST['no_hp'] ?? ''; // Kolom no_hp
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? ''; // Kolom jenis_kelamin
    $password = $_POST['password'] ?? '';

    // Jika password diubah, enkripsi password tersebut
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE akun_admin SET nama_admin = ?, alamat = ?, no_hp = ?, jenis_kelamin = ?, password = ? WHERE id_admin = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssssss", $nama_admin, $alamat, $no_hp, $jenis_kelamin, $hashed_password, $_SESSION['admin_id']);
    } else {
        // Jika password tidak diubah, update data tanpa password
        $updateQuery = "UPDATE akun_admin SET nama_admin = ?, alamat = ?, no_hp = ?, jenis_kelamin = ? WHERE id_admin = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssss", $nama_admin, $alamat, $no_hp, $jenis_kelamin, $_SESSION['admin_id']);
    }

    // Eksekusi query update
    if ($stmt->execute()) {
        // Jika berhasil, update data di session dan beri pesan sukses
        $_SESSION['message'] = "Data berhasil diperbarui!";
        header("Location: akunAdmin.php"); // Redirect ke halaman akun untuk melihat perubahan
        exit;
    } else {
        // Tampilkan pesan error jika query update gagal
        $error_message = "Error saat memperbarui data: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Akun Admin - Salon</title>
    <link rel="stylesheet" href="akunstyle.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="dashboardAdmin.php">Home</a></li>
                <li><a href="pricelistAdmin.php">Pricelist</a></li>
                <li><a href="reservasiAdmin.php">Reservasi</a></li>
                <li><a href="managementPengguna.php">Pengguna</a></li>
                <li><a href="akunAdmin.php">Akun Saya</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <h2>Informasi Akun Admin</h2>

        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <!-- Menampilkan pesan sukses jika ada -->
        <?php if (isset($_SESSION['message'])): ?>
            <p style="color: green;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>

        <!-- Menampilkan data akun admin -->
        <form action="akunAdmin.php" method="POST">
            <div class="form-group">
                <label for="id_admin">ID Admin:</label>
                <input type="text" id="id_admin" name="id_admin" value="<?= htmlspecialchars($admin['id_admin'] ?? ''); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="nama_admin">Nama Lengkap:</label>
                <input type="text" id="nama_admin" name="nama_admin" value="<?= htmlspecialchars($admin['nama_admin'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <input type="text" id="alamat" name="alamat" value="<?= htmlspecialchars($admin['alamat'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="no_hp">No HP:</label>
                <input type="text" id="no_hp" name="no_hp" value="<?= htmlspecialchars($admin['no_hp'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin:</label>
                <select id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="L" <?= isset($admin['jenis_kelamin']) && $admin['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="P" <?= isset($admin['jenis_kelamin']) && $admin['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password (Kosongkan jika tidak ingin mengubah):</label>
                <input type="password" id="password" name="password">
            </div>

            <button type="submit">Perbarui Data</button>
        </form>

        <br>

        <h3>Detail Akun Admin:</h3>
        <ul>
            <li><strong>ID Admin:</strong> <?= htmlspecialchars($admin['id_admin'] ?? ''); ?></li>
            <li><strong>Nama Lengkap:</strong> <?= htmlspecialchars($admin['nama_admin'] ?? ''); ?></li>
            <li><strong>Alamat:</strong> <?= htmlspecialchars($admin['alamat'] ?? ''); ?></li>
            <li><strong>No HP:</strong> <?= htmlspecialchars($admin['no_hp'] ?? ''); ?></li>
            <li><strong>Jenis Kelamin:</strong> <?= htmlspecialchars($admin['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'); ?></li>
        </ul>

        <br>

        <a href="logout.php" class="logout-btn">Log Out</a>
    </main>
    
    <footer>
        <p>&copy; 2024 Salon Kecantikan. All rights reserved.</p>
    </footer>
</body>
</html>

<?php
// Tutup koneksi setelah halaman selesai
$conn->close();
?>
