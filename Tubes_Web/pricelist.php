<?php
// Memulai sesi untuk login, jika diperlukan
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['id'])) {
    // Jika belum login, alihkan ke halaman login
    header("Location: login.php");
    exit;
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "salon_database";

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fungsi untuk membaca data layanan
$result = $conn->query("SELECT * FROM pricelist");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noir Salon - Pricelist</title>
    <link rel="stylesheet" href="style.css">
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
        <!-- Teks sebelum tabel -->
        <div class="info-text">
            Nikmati segala jenis pelayanan dari kami dengan harga yang terjangkau.
        </div>

        <!-- Tabel layanan -->
        <table>
            <thead>
                <tr>
                    <th>Layanan</th>
                    <th>Durasi</th>
                    <th>Harga</th>
                    <th>Gambar</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['layanan']); ?></td>
                        <td><?= htmlspecialchars($row['durasi']); ?></td>
                        <td><?= htmlspecialchars($row['harga']); ?></td>
                        <td>
                            <?php if (!empty($row['gambar'])): ?>
                                <img src="uploads/<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['layanan']); ?>" width="100">
                            <?php else: ?>
                                <img src="uploads/no-image.png" alt="No Image" width="100"> <!-- Gambar default jika tidak ada gambar -->
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    
    <footer>
        <p>&copy; 2024 Noir Salon. All rights reserved.</p>
    </footer>
</body>
</html>

<?php $conn->close(); ?>
