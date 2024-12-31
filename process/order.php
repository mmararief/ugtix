<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'ugtix');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start transaction
$conn->begin_transaction();

try {
    // Prepare and bind order insertion
    $stmt = $conn->prepare("INSERT INTO pesanan (email, nama, npm, tanggal, id_event, metode, total) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssisi", $email, $nama, $npm, $tanggal, $id_event, $metode, $total);

    // Set parameters and execute
    $email = $_POST['email'];
    $nama = $_POST['nama'];
    $npm = $_POST['npm'];
    $tanggal = $_POST['tanggal'];
    $id_event = $_POST['id_event'];
    $metode = $_POST['metode'];
    $total = $_POST['total'];
    $stmt->execute();
    $order_id = $conn->insert_id;

    // Generate UUID and ticket code
    $ticket_uuid = generateUUID();
    $kode_tiket = generateTicketCode();

    // Insert ticket
    $ticket_stmt = $conn->prepare("INSERT INTO tiket (id, id_pesanan, id_event, kode_tiket, status) VALUES (?, ?, ?, ?, 'active')");
    $ticket_stmt->bind_param("siis", $ticket_uuid, $order_id, $id_event, $kode_tiket);
    $ticket_stmt->execute();

    // Update pesanan with ticket ID
    $update_stmt = $conn->prepare("UPDATE pesanan SET id_tiket = ? WHERE id = ?");
    $update_stmt->bind_param("si", $ticket_uuid, $order_id);
    $update_stmt->execute();

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'ticket_id' => $ticket_uuid
    ]);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Helper function to generate UUID v4
function generateUUID()
{
    if (function_exists('random_bytes')) {
        $data = random_bytes(16);
    } elseif (function_exists('openssl_random_pseudo_bytes')) {
        $data = openssl_random_pseudo_bytes(16);
    } else {
        throw new Exception('Cannot generate UUID: no cryptographically secure random function available');
    }

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // Set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // Set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// Helper function to generate readable ticket code
function generateTicketCode()
{
    $prefix = 'TIX';
    $timestamp = time();
    $random = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
    return $prefix . '-' . $timestamp . '-' . $random;
}

// Close connections
$stmt->close();
$ticket_stmt->close();
$conn->close();
