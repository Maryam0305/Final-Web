<?php
// Memulai sesi untuk login, jika diperlukan
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['id'])) {
    // Jika belum login, alihkan ke halaman login
    header("Location: login.php");
    exit;
}

// Kode untuk halaman dashboard, jika pengguna sudah login
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noir Salon - Dashboard</title>
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
        <section class="hero">
            <h1>NOIR</h1>
            <p>#semuaberhakcakep</p>
            <p>Salon kecantikan yang akan mewujudkan semua penampilan impianmu</p>
        </section>

        <section class="image-gallery">
            <div class="image-frame">
                <div class="images">
                    <img src="source/img1.jpg" alt="Hairstyle 1">
                    <!-- Gambar lainnya bisa ditambahkan di sini -->
                </div>
            </div>

            <div class="info">
                <p>Alamat: jl. Tun Abdul Razak, Gowa</p>
                <p>Jam operasional: pukul 09.00 - 20.00 WITA</p>
                <p><strong>Reservasi sekarang juga!</strong></p>
            </div>
        </section>
    </main>
    
    <footer>
        <p>&copy; <?= date('Y'); ?> Noir Salon. All rights reserved.</p>
    </footer>
</body>
</html>
