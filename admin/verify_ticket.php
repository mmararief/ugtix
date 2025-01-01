<?php
session_start();
// if (!isset($_SESSION['admin_id'])) {
//     header('Content-Type: application/json');
//     echo json_encode(['success' => false, 'message' => 'Unauthorized']);
//     exit;
// }

require_once '../process/koneksi.php';

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);
$kode_tiket = $data['kode_tiket'] ?? '';
$event_id = $data['event_id'] ?? '';

// Verify ticket - Perbaikan query SQL
$query = "SELECT t.id, t.kode_tiket, t.status,
                 p.nama, p.email, p.npm 
          FROM tiket t 
          JOIN pesanan p ON t.id_pesanan = p.id 
          WHERE t.kode_tiket = ? AND t.id_event = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("si", $kode_tiket, $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $ticket = $result->fetch_assoc();

    if ($ticket['status'] === 'digunakan') {


        echo json_encode([
            'success' => false,
            'status' => 'digunakan',
            'message' => 'Tiket sudah digunakan',
            'ticket_info' => [
                'nama' => $ticket['nama'],
                'email' => $ticket['email'],
                'npm' => $ticket['npm'],

            ]
        ]);
        exit;
    }

    // Update ticket status to used and set last_used timestamp
    $update = "UPDATE tiket SET status = 'digunakan' WHERE kode_tiket = ? AND id_event = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("si", $kode_tiket, $event_id);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'status' => 'valid',
            'message' => 'Tiket berhasil divalidasi',
            'nama' => $ticket['nama'],
            'email' => $ticket['email'],
            'npm' => $ticket['npm']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'status' => 'error',
            'message' => 'Gagal mengupdate status tiket'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'status' => 'invalid',
        'message' => 'Tiket tidak valid atau bukan untuk event ini'
    ]);
}
