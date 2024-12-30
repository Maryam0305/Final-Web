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

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Hapus reservasi
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $sqlDelete = "DELETE FROM reservasi WHERE id = $id";
    if ($conn->query($sqlDelete) === TRUE) {
        echo "<p style='color: green;'>Reservasi berhasil dihapus.</p>";
    } else {
        echo "<p style='color: red;'>Gagal menghapus reservasi: " . $conn->error . "</p>";
    }
}

// Update reservasi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
    $editId = $_POST['edit_id'];
    $editNama = $_POST['edit_nama'];
    $editNoHp = $_POST['edit_noHp'];
    $editLayanan = $_POST['edit_layanan'];
    $editTanggal = $_POST['edit_tanggal'];

    $sqlUpdate = "UPDATE reservasi SET nama = '$editNama', noHp = '$editNoHp', layanan = '$editLayanan', tanggal = '$editTanggal' WHERE id = $editId";

    if ($conn->query($sqlUpdate) === TRUE) {
        echo "<p style='color: green;'>Reservasi berhasil diperbarui.</p>";
    } else {
        echo "<p style='color: red;'>Gagal memperbarui reservasi: " . $conn->error . "</p>";
    }
}

// Ambil data reservasi
$sqlReservasi = "SELECT reservasi.id, reservasi.nama, reservasi.noHp, pricelist.layanan, reservasi.tanggal 
                 FROM reservasi 
                 JOIN pricelist ON reservasi.layanan = pricelist.id";
$resultReservasi = $conn->query($sqlReservasi);

// Ambil data layanan untuk dropdown edit
$sqlPricelist = "SELECT id, layanan FROM pricelist";
$resultPricelist = $conn->query($sqlPricelist);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reservasi</title>
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
        <h2>Daftar Reservasi</h2>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>No. HP</th>
                    <th>Layanan</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultReservasi->num_rows > 0) {
                    $no = 1;
                    while ($row = $resultReservasi->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . $row['nama'] . "</td>";
                        echo "<td>" . $row['noHp'] . "</td>";
                        echo "<td>" . $row['layanan'] . "</td>";
                        echo "<td>" . $row['tanggal'] . "</td>";
                        echo "<td>
                                <a href='?edit=" . $row['id'] . "'>Edit</a> | 
                                <a href='?hapus=" . $row['id'] . "' onclick='return confirm(\"Yakin ingin menghapus reservasi ini?\")'>Selesai</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Tidak ada reservasi.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <?php
        // Form edit jika tombol edit ditekan
        if (isset($_GET['edit'])) {
            $editId = $_GET['edit'];
            $sqlEdit = "SELECT * FROM reservasi WHERE id = $editId";
            $resultEdit = $conn->query($sqlEdit);
            if ($resultEdit->num_rows > 0) {
                $editData = $resultEdit->fetch_assoc();
                ?>
                <h2>Edit Reservasi</h2>
                <form action="reservasiAdmin.php" method="POST">
                    <input type="hidden" name="edit_id" value="<?= $editData['id'] ?>">
                    <label for="edit_nama">Nama Lengkap:</label>
                    <input type="text" id="edit_nama" name="edit_nama" value="<?= $editData['nama'] ?>" required>

                    <label for="edit_noHp">No. HP:</label>
                    <input type="text" id="edit_noHp" name="edit_noHp" value="<?= $editData['noHp'] ?>" required>

                    <label for="edit_layanan">Layanan:</label>
                    <select id="edit_layanan" name="edit_layanan" required>
                        <?php
                        if ($resultPricelist->num_rows > 0) {
                            while ($service = $resultPricelist->fetch_assoc()) {
                                $selected = ($service['id'] == $editData['layanan']) ? "selected" : "";
                                echo "<option value='" . $service['id'] . "' $selected>" . $service['layanan'] . "</option>";
                            }
                        }
                        ?>
                    </select>

                    <label for="edit_tanggal">Tanggal:</label>
                    <input type="date" id="edit_tanggal" name="edit_tanggal" value="<?= $editData['tanggal'] ?>" required>

                    <button type="submit">Simpan</button>
                    <a href="reservasiAdmin.php">Batal</a>
                </form>
                <?php
            }
        }
        ?>
    </main>
    <footer>
        <p>© 2024 Salon Kecantikan. All rights reserved.</p>
    </footer>
</body>
</html>
