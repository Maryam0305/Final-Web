<?php
session_start();

// Cek apakah pengguna sudah login sebagai admin
if (isset($_SESSION['admin_id'])) {
    // Jika admin, akses ke dashboard admin
    echo "Selamat datang Admin!";
    // Redirect ke halaman admin atau tampilkan konten dashboard admin
} elseif (isset($_SESSION['id'])) {
    // Jika pengguna biasa, akses ke dashboard pengguna
    echo "Selamat datang Pengguna!";
    // Redirect ke halaman pengguna atau tampilkan konten dashboard pengguna
} else {
    // Jika belum login, alihkan ke halaman login
    header("Location: login.php");
    exit;
}
?>
