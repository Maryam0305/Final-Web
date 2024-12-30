<?php
// Memulai sesi untuk login, jika diperlukan
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['admin_id'])) {
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

// Fungsi untuk menambah data layanan
if (isset($_POST['add'])) {
    $layanan = $_POST['layanan'];
    $durasi = $_POST['durasi'];
    $harga = $_POST['harga'];
    $gambar = $_FILES['gambar']['name'];
    $target = "uploads/" . basename($gambar);

    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
        $sql = "INSERT INTO pricelist (layanan, durasi, harga, gambar) VALUES ('$layanan', '$durasi', '$harga', '$gambar')";
        $conn->query($sql);
    }
}

// Fungsi untuk menghapus data layanan
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM pricelist WHERE id=$id");
}

// Fungsi untuk mengedit data layanan
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $layanan = $_POST['layanan'];
    $durasi = $_POST['durasi'];
    $harga = $_POST['harga'];
    $gambar = $_FILES['gambar']['name'];

    // Jika gambar baru tidak di-upload, gunakan gambar lama
    if ($gambar == "") {
        $gambar = $_POST['old_gambar'];
    } else {
        $target = "uploads/" . basename($gambar);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $target);
    }

    $sql = "UPDATE pricelist SET layanan='$layanan', durasi='$durasi', harga='$harga', gambar='$gambar' WHERE id=$id";
    $conn->query($sql);
}

// Fungsi untuk membaca data layanan
$result = $conn->query("SELECT * FROM pricelist");

// Cek jika ada ID untuk edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editData = $conn->query("SELECT * FROM pricelist WHERE id=$id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noir Salon</title>
    <link rel="stylesheet" href="style.css">
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
        <!-- Teks sebelum tabel -->
        <div class="info-text">
            Nikmati segala jenis pelayanan dari kami dengan harga yang terjangkau.
        </div>

        <!-- Form untuk menambah atau mengedit layanan -->
        <h2><?= $editData ? 'Edit Layanan' : 'Tambah Layanan' ?></h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $editData['id'] ?? ''; ?>">
            <input type="text" name="layanan" value="<?= $editData['layanan'] ?? ''; ?>" placeholder="Nama Layanan" required>
            <input type="text" name="durasi" value="<?= $editData['durasi'] ?? ''; ?>" placeholder="Durasi" required>
            <input type="text" name="harga" value="<?= $editData['harga'] ?? ''; ?>" placeholder="Harga" required>
            
            <!-- Foto layanan, menampilkan foto lama jika ada -->
            <input type="file" name="gambar">
            <?php if ($editData): ?>
                <p>Foto Lama: <img src="uploads/<?= $editData['gambar']; ?>" alt="Layanan" width="100"></p>
                <input type="hidden" name="old_gambar" value="<?= $editData['gambar']; ?>">
            <?php endif; ?>
            
            <button type="submit" name="<?= $editData ? 'edit' : 'add'; ?>">Simpan Layanan</button>
        </form>

        <!-- Tabel layanan -->
        <table>
            <thead>
                <tr>
                    <th>Layanan</th>
                    <th>Durasi</th>
                    <th>Harga</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $row['layanan']; ?></td>
                        <td><?= $row['durasi']; ?></td>
                        <td><?= $row['harga']; ?></td>
                        <td><img src="uploads/<?= $row['gambar']; ?>" alt="<?= $row['layanan']; ?>" width="100"></td>
                        <td>
                            <a href="?edit=<?= $row['id']; ?>">Edit</a> | 
                            <a href="?delete=<?= $row['id']; ?>" onclick="return confirm('Hapus layanan ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    
    <footer>
        <p>&copy; 2024 Salon Kecantikan. All rights reserved.</p>
    </footer>
</body>
</html>

<?php $conn->close(); ?>
