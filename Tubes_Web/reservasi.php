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

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Cek jika form telah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $noHp = $_POST['noHP'];
    $layanan = $_POST['layanan'];
    $tanggal = $_POST['tanggal'];

    // Menyimpan data ke tabel reservasi
    $sql = "INSERT INTO reservasi (nama, noHp, layanan, tanggal) VALUES ('$nama', '$noHp', '$layanan', '$tanggal')";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>Reservasi berhasil dibuat!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }
}

// Ambil data layanan dari tabel pricelist
$queryLayanan = "SELECT id, layanan FROM pricelist";
$resultLayanan = $conn->query($queryLayanan);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noir Salon - Reservasi</title>
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
        </nav>
    </header>
    <main>
        <div class="info-text">
            <h2>Formulir Reservasi</h2>
            <p>Silahkan mengisi formulir di bawah untuk melakukan reservasi.</p>
        </div>
        <form action="reservasi.php" method="POST">
            <label for="nama">Nama Lengkap:</label>
            <input type="text" id="nama" name="nama" placeholder="Nama Anda" required>

            <label for="noHp">No. Telepon:</label>
            <input type="text" id="noHp" name="noHP" placeholder="Contoh: 081234567890" required>

            <label for="layanan">Pilih Layanan:</label>
            <select id="layanan" name="layanan" required>
                <option value="" disabled selected>Pilih layanan</option>
                <?php
                if ($resultLayanan->num_rows > 0) {
                    while ($row = $resultLayanan->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['layanan'] . "</option>";
                    }
                } else {
                    echo "<option value=''>Tidak ada layanan tersedia</option>";
                }
                ?>
            </select>

            <label for="tanggal">Pilih Tanggal:</label>
            <input type="date" id="tanggal" name="tanggal" required>

            <button type="submit">Kirim</button>
        </form>
    </main>
    <footer>
        <p>© 2024 Salon Kecantikan. All rights reserved.</p>
    </footer>
</body>
</html>
