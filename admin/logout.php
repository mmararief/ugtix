<?php
// Memulai sesi
session_start();

// Menghapus semua variabel sesi
$_SESSION = [];

// Menghancurkan sesi
session_destroy();

// Mengarahkan pengguna kembali ke halaman login
header("Location: login.php");
exit;
