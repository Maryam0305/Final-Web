<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['id'])) {
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

// Ambil data pengguna berdasarkan user_id dari sesi
$user_id = $_SESSION['id']; // Pastikan $_SESSION['id'] sudah terisi
$query = "SELECT * FROM akun WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah query berhasil dan ada data pengguna
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Menampilkan pesan error jika data pengguna tidak ditemukan
    $error_message = "Pengguna tidak ditemukan. Pastikan Anda sudah login.";
}

// Proses pembaruan data pengguna jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form dan validasi
    $nama = htmlspecialchars($_POST['nama']);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $gender = $_POST['gender'];
    $alamat = htmlspecialchars($_POST['alamat']);
    $password = $_POST['password'];

    // Jika password diubah, enkripsi password tersebut
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE akun SET nama = ?, tanggal_lahir = ?, gender = ?, alamat = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssssi", $nama, $tanggal_lahir, $gender, $alamat, $hashed_password, $user_id);
    } else {
        // Jika password tidak diubah, update data tanpa password
        $updateQuery = "UPDATE akun SET nama = ?, tanggal_lahir = ?, gender = ?, alamat = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssssi", $nama, $tanggal_lahir, $gender, $alamat, $user_id);
    }

    // Eksekusi query update
    if ($stmt->execute()) {
        // Jika berhasil, update data di session dan beri pesan sukses
        $_SESSION['message'] = "Data berhasil diperbarui!";
        header("Location: akun.php"); // Redirect ke halaman akun untuk melihat perubahan
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
    <title>Akunku - Noir Salon</title>
    <link rel="stylesheet" href="akunstyle.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="pricelist.php">Pricelist</a></li>
                <li><a href="reservasi.php">Reservasi</a></li>
                <li><a href="akun.php">Akun Saya</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <h2>Informasi Akun</h2>

        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="akun.php" method="POST">
            <div class="form-group">
                <label for="nama">Nama Lengkap:</label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($user['nama']); ?>" required>
            </div>

            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir:</label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?= htmlspecialchars($user['tanggal_lahir']); ?>" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="Laki-laki" <?= $user['gender'] == 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="Perempuan" <?= $user['gender'] == 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <input type="text" id="alamat" name="alamat" value="<?= htmlspecialchars($user['alamat']); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password (Kosongkan jika tidak ingin mengubah):</label>
                <input type="password" id="password" name="password">
            </div>

            <button type="submit">Perbarui Data</button>
        </form>

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
