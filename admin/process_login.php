<?php
session_start();
require_once '../process/koneksi.php'; // Pastikan koneksi ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        echo "Email dan password wajib diisi!";
        exit;
    }

    // Query untuk mendapatkan data admin berdasarkan email
    $stmt = $conn->prepare("SELECT id, nama, password FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        // Verifikasi password
        if ($password === $admin['password']) {
            // Set sesi login
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama'];
            header("Location: index.php");
            exit;
        } else {
            echo "Password salah!";
        }
    } else {
        echo "Email tidak terdaftar!";
    }

    $stmt->close();
} else {
    echo "Metode request tidak valid!";
}
