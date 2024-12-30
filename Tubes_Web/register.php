<?php
session_start();

// Koneksi ke database
$host = "localhost";
$user = "root";
$password = "";
$database = "salon_database";
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $gender = $_POST['gender'];
    $alamat = $_POST['alamat'];
    $password = $_POST['password'];

    // Periksa apakah nama sudah terdaftar
    $check_sql = "SELECT * FROM akun WHERE nama = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $nama);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $error = "Nama sudah terdaftar.";
    } else {
        // Simpan data pengguna ke tabel akun
        $register_sql = "INSERT INTO akun (nama, tanggal_lahir, gender, alamat, password) VALUES (?, ?, ?, ?, ?)";
        $register_stmt = $conn->prepare($register_sql);
        $register_stmt->bind_param("sssss", $nama, $tanggal_lahir, $gender, $alamat, $password);
        $register_stmt->execute();

        // Jika berhasil, redirect ke halaman login
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f7f7f7; }
        .container { width: 100%; max-width: 400px; padding: 40px; background: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); text-align: center; }
        label, input, select { display: block; width: 100%; margin-bottom: 15px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px; width: 100%; background-color: #d4af37; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #8b5a2b; }
        .message { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registrasi</h2>
        <?php if (isset($error)) echo "<p class='message'>$error</p>"; ?>
        <form method="post">
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" required>

            <label for="tanggal_lahir">Tanggal Lahir</label>
            <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>

            <label for="gender">Gender</label>
            <select id="gender" name="gender">
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>

            <label for="alamat">Alamat</label>
            <input type="text" id="alamat" name="alamat" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" name="register">Register</button>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>
