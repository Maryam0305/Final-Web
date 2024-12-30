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

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek apakah login sebagai admin atau pengguna
    if (isset($_POST['admin_login']) && $_POST['admin_login'] == 'on') {
        // Login sebagai admin, menggunakan id_admin
        $sql = "SELECT * FROM akun_admin WHERE id_admin = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
    } else {
        // Login sebagai pengguna, menggunakan nama
        $sql = "SELECT * FROM akun WHERE nama = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Cek apakah ada akun yang cocok
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Set sesi dengan id pengguna atau admin
        if (isset($_POST['admin_login']) && $_POST['admin_login'] == 'on') {
            $_SESSION['admin_id'] = $row['id_admin'];  // Menyimpan sesi admin
        } else {
            $_SESSION['id'] = $row['id'];  // Menyimpan sesi pengguna biasa
        }

        // Redirect berdasarkan jenis login
        if (isset($_POST['admin_login']) && $_POST['admin_login'] == 'on') {
            header("Location: dashboardAdmin.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        $error = "Nama atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f7f7f7; }
        .container { width: 100%; max-width: 400px; padding: 40px; background: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); text-align: center; }
        label, input { display: block; width: 100%; margin-bottom: 15px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px; width: 100%; background-color: #d4af37; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #8b5a2b; }
        .message { color: red; }
        .info-text { margin-bottom: 20px; color: #555; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <!-- Keterangan untuk admin -->
        <div class="info-text">
            Jika Anda adalah admin, gunakan id admin Anda untuk login.
        </div>

        <?php if (isset($error)) echo "<p class='message'>$error</p>"; ?>

        <form method="post">
            <!-- Input untuk nama/admin ID -->
            <label for="username">Nama / ID Admin</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <!-- Kotak centang untuk login sebagai admin -->
            <label>
                <input type="checkbox" name="admin_login" value="on">
                Login sebagai admin
            </label>

            <button type="submit" name="login">Login</button>
        </form>

        <div>
            <a href="register.php">Register</a>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
