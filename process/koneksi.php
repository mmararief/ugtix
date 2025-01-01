<?php
// Konfigurasi database
$host = 'localhost:3305';      // Nama host database
$user = 'root';           // Username database
$password = '';           // Password database
$dbname = 'ugtix';        // Nama database

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}
