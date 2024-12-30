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

// Hapus pengguna
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $sqlDelete = "DELETE FROM akun WHERE id = $id";
    if ($conn->query($sqlDelete) === TRUE) {
        echo "<p style='color: green;'>Pengguna berhasil dihapus.</p>";
    } else {
        echo "<p style='color: red;'>Gagal menghapus pengguna: " . $conn->error . "</p>";
    }
}

// Update pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
    $editId = $_POST['edit_id'];
    $editNama = $_POST['edit_nama'];
    $editTanggalLahir = $_POST['edit_tanggal_lahir'];
    $editGender = $_POST['edit_gender'];
    $editAlamat = $_POST['edit_alamat'];
    $editPassword = $_POST['edit_password'];

    // Enkripsi password jika diubah
    if (!empty($editPassword)) {
        $hashedPassword = password_hash($editPassword, PASSWORD_DEFAULT);
        $sqlUpdate = "UPDATE akun SET nama = '$editNama', tanggal_lahir = '$editTanggalLahir', gender = '$editGender', alamat = '$editAlamat', password = '$hashedPassword' WHERE id = $editId";
    } else {
        $sqlUpdate = "UPDATE akun SET nama = '$editNama', tanggal_lahir = '$editTanggalLahir', gender = '$editGender', alamat = '$editAlamat' WHERE id = $editId";
    }

    if ($conn->query($sqlUpdate) === TRUE) {
        echo "<p style='color: green;'>Pengguna berhasil diperbarui.</p>";
    } else {
        echo "<p style='color: red;'>Gagal memperbarui pengguna: " . $conn->error . "</p>";
    }
}

// Create pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_nama'])) {
    $createNama = $_POST['create_nama'];
    $createTanggalLahir = $_POST['create_tanggal_lahir'];
    $createGender = $_POST['create_gender'];
    $createAlamat = $_POST['create_alamat'];
    $createPassword = $_POST['create_password'];

    $hashedPassword = password_hash($createPassword, PASSWORD_DEFAULT);

    $sqlCreate = "INSERT INTO akun (nama, tanggal_lahir, gender, alamat, password) VALUES ('$createNama', '$createTanggalLahir', '$createGender', '$createAlamat', '$hashedPassword')";

    if ($conn->query($sqlCreate) === TRUE) {
        echo "<p style='color: green;'>Pengguna baru berhasil ditambahkan.</p>";
    } else {
        echo "<p style='color: red;'>Gagal menambahkan pengguna: " . $conn->error . "</p>";
    }
}

// Ambil data pengguna
$sqlPengguna = "SELECT * FROM akun";
$resultPengguna = $conn->query($sqlPengguna);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Pengguna</title>
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
        <h2>Daftar Pengguna</h2>
        
        <button onclick="toggleCreateForm()">Tambah Pengguna</button>
        
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Tanggal Lahir</th>
                    <th>Gender</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultPengguna->num_rows > 0) {
                    $no = 1;
                    while ($row = $resultPengguna->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . $row['nama'] . "</td>";
                        echo "<td>" . $row['tanggal_lahir'] . "</td>";
                        echo "<td>" . ($row['gender'] == 'L' ? 'Laki-laki' : 'Perempuan') . "</td>";
                        echo "<td>" . $row['alamat'] . "</td>";
                        echo "<td>
                                <a href='?edit=" . $row['id'] . "'>Edit</a> | 
                                <a href='?hapus=" . $row['id'] . "' onclick='return confirm(\"Yakin ingin menghapus pengguna ini?\")'>Hapus</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Tidak ada pengguna.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <?php
        // Form edit jika tombol edit ditekan
        if (isset($_GET['edit'])) {
            $editId = $_GET['edit'];
            $sqlEdit = "SELECT * FROM akun WHERE id = $editId";
            $resultEdit = $conn->query($sqlEdit);
            if ($resultEdit->num_rows > 0) {
                $editData = $resultEdit->fetch_assoc();
                ?>
                <h2>Edit Pengguna</h2>
                <form action="managementPengguna.php" method="POST">
                    <input type="hidden" name="edit_id" value="<?= $editData['id'] ?>">
                    <label for="edit_nama">Nama Lengkap:</label>
                    <input type="text" id="edit_nama" name="edit_nama" value="<?= $editData['nama'] ?>" required>

                    <label for="edit_tanggal_lahir">Tanggal Lahir:</label>
                    <input type="date" id="edit_tanggal_lahir" name="edit_tanggal_lahir" value="<?= $editData['tanggal_lahir'] ?>" required>

                    <label for="edit_gender">Gender:</label>
                    <select id="edit_gender" name="edit_gender" required>
                        <option value="L" <?= $editData['gender'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= $editData['gender'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>

                    <label for="edit_alamat">Alamat:</label>
                    <input type="text" id="edit_alamat" name="edit_alamat" value="<?= $editData['alamat'] ?>" required>

                    <label for="edit_password">Password (kosongkan jika tidak diubah):</label>
                    <input type="password" id="edit_password" name="edit_password">

                    <button type="submit">Simpan</button>
                    <a href="managementPengguna.php">Batal</a>
                </form>
                <?php
            }
        }

        // Form create pengguna jika tombol tambah pengguna ditekan
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_nama'])) {
            ?>
            <h2>Tambah Pengguna</h2>
            <form action="managementPengguna.php" method="POST">
                <label for="create_nama">Nama Lengkap:</label>
                <input type="text" id="create_nama" name="create_nama" required>

                <label for="create_tanggal_lahir">Tanggal Lahir:</label>
                <input type="date" id="create_tanggal_lahir" name="create_tanggal_lahir" required>

                <label for="create_gender">Gender:</label>
                <select id="create_gender" name="create_gender" required>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>

                <label for="create_alamat">Alamat:</label>
                <input type="text" id="create_alamat" name="create_alamat" required>

                <label for="create_password">Password:</label>
                <input type="password" id="create_password" name="create_password" required>

                <button type="submit">Simpan</button>
                <a href="managementPengguna.php">Batal</a>
            </form>
            <?php
        }
        ?>
    </main>
    <footer>
        <p>© 2024 Salon Kecantikan. All rights reserved.</p>
    </footer>

    <script>
        // Toggle form create pengguna
        function toggleCreateForm() {
            const formCreate = document.querySelector('form[action="managementPengguna.php"]');
            formCreate.style.display = formCreate.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
