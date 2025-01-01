<?php
session_start();

if (isset($_GET['reset'])) {
    unset($_SESSION['history_step']);
    unset($_SESSION['history_email']);
    header('Location: history.php');
    exit;
}

// Function to call API
function callAPI($url, $data)
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json']
    ]);
    $response = curl_exec($curl);

    // Add error handling
    if ($response === false) {
        error_log('cURL Error: ' . curl_error($curl));
        curl_close($curl);
        return ['error' => 'Connection failed'];
    }

    curl_close($curl);
    $decoded = json_decode($response, true);

    // Check for JSON decode errors
    if ($decoded === null) {
        error_log('JSON Decode Error: ' . $response);
        return ['error' => 'Invalid response'];
    }

    return $decoded;
}

$error = '';
$orders = [];
$step = isset($_SESSION['history_step']) ? $_SESSION['history_step'] : 1;

// If we're on step 3, get orders from session
if ($step == 3 && isset($_SESSION['orders'])) {
    $orders = $_SESSION['orders'];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_email'])) {
        $email = $_POST['email'];
        $_SESSION['history_email'] = $email;

        // Send OTP request with improved error handling
        $response = callAPI('https://ugtix.dapurkynan.com/send-otp', ['email' => $email]);
        if ($response && isset($response['success'])) {
            $_SESSION['history_step'] = 2;
            header('Location: history.php');
            exit;
        } else {
            $error = isset($response['error'])
                ? 'Error: ' . $response['error']
                : 'Failed to send OTP. Please try again.';
            error_log('OTP Send Error: ' . json_encode($response));
        }
    } elseif (isset($_POST['verify_otp'])) {
        $email = $_SESSION['history_email'];
        $otp = $_POST['otp'];

        // Verify OTP
        $response = callAPI('https://ugtix.dapurkynan.com/verify-otp', [
            'email' => $email,
            'otp' => $otp
        ]);

        if ($response && isset($response['success']) && $response['success'] === true && isset($response['message']) && $response['message'] === "OTP verified successfully") {
            $_SESSION['history_step'] = 3;
            // Fetch orders from database with joined event details
            require_once 'process/koneksi.php';
            $stmt = $conn->prepare("
                SELECT p.*, e.nama as event_nama, e.tanggal as event_tanggal, 
                       e.waktu, e.lokasi, e.harga, t.kode_tiket
                FROM pesanan p 
                JOIN events e ON p.id_event = e.id 
                JOIN tiket t ON p.id_tiket = t.id
                WHERE p.email = ? 
                ORDER BY p.tanggal DESC");
            $stmt->execute([$email]);
            $result = $stmt->get_result();
            $orders = $result->fetch_all(MYSQLI_ASSOC);

            // Store orders in session
            $_SESSION['orders'] = $orders;

            header('Location: history.php');
            exit;
        } else {
            $error = isset($response['error']) ? $response['error'] : 'Invalid OTP. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a1464;
            --accent-color: #9eff00;
            --dark-bg: #1a1a1a;
        }

        body {
            background-color: var(--dark-bg);
            color: #fff;
            min-height: 100vh;
        }

        .container {
            padding-top: 3rem;
            padding-bottom: 3rem;
        }

        h2 {
            color: var(--accent-color);
            font-weight: 700;
            margin-bottom: 2rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 3px solid var(--accent-color);
            padding-bottom: 1rem;
        }

        .card {
            background-color: rgba(26, 20, 100, 0.3);
            border: 1px solid var(--primary-color);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 2rem;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--primary-color);
            color: #fff;
            border-radius: 8px;
            padding: 0.8rem;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: var(--accent-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(158, 255, 0, 0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.8rem 2rem;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: var(--dark-bg);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: transparent;
            border: 2px solid var(--accent-color);
            color: var(--accent-color);
            padding: 0.8rem 2rem;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: var(--accent-color);
            color: var(--dark-bg);
            transform: translateY(-2px);
        }

        .table {
            color: #fff;
            border-radius: 15px;
            overflow: hidden;
            margin-top: 2rem;
        }

        .table thead {
            background-color: var(--primary-color);
        }

        .table th {
            color: var(--accent-color);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 1rem;
            border: none;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .table tbody tr {
            background-color: rgba(26, 20, 100, 0.2);
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(26, 20, 100, 0.4);
        }

        .text-muted {
            color: rgba(255, 255, 255, 0.6) !important;
        }

        .alert {
            border-radius: 8px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border-color: #dc3545;
            color: #fff;
        }

        .alert-info {
            background-color: rgba(13, 202, 240, 0.2);
            border-color: #0dcaf0;
            color: #fff;
        }

        /* Ticket styles */
        .ticket {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            color: #000;
        }

        .ticket-header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .ticket-header h2 {
            color: white;
            margin: 0;
            padding: 0;
            border: none;
        }

        .ticket-body {
            padding: 30px;
            position: relative;
        }

        .ticket-info {
            margin-bottom: 20px;
        }

        .ticket-info h5 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .ticket-number {
            font-size: 0.9rem;
            color: #666;
            position: absolute;
            bottom: 10px;
            right: 20px;
        }

        .diagonal-line {
            position: absolute;
            top: 0;
            right: 150px;
            height: 100%;
            width: 3px;
            background: repeating-linear-gradient(45deg,
                    #ccc,
                    #ccc 10px,
                    #fff 10px,
                    #fff 20px);
        }

        .modal-content {
            background-color: transparent;
            border: none;
        }

        .modal-footer {
            background-color: var(--dark-bg);
            border-top: 1px solid var(--primary-color);
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container">
        <h2>Order History</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Enter your email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <button type="submit" name="submit_email" class="btn btn-primary">Send OTP</button>
                    </form>
                </div>
            </div>

        <?php elseif ($step == 2): ?>
            <div class="card">
                <div class="card-body">
                    <p>OTP has been sent to <?php echo htmlspecialchars($_SESSION['history_email']); ?></p>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="otp" class="form-label">Enter OTP</label>
                            <input type="text" class="form-control" id="otp" name="otp" required>
                        </div>
                        <button type="submit" name="verify_otp" class="btn btn-primary">Verify OTP</button>
                        <a href="history.php?reset=1" class="btn btn-secondary ms-2">Back</a>
                    </form>
                </div>
            </div>

        <?php elseif ($step == 3): ?>
            <?php if (empty($orders)): ?>
                <div class="alert alert-info">No orders found for this email.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Name</th>
                                <th>NPM</th>
                                <th>Event</th>
                                <th>Date</th>
                                <th>Payment Method</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($order['npm']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($order['event_nama']); ?><br>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($order['lokasi']); ?><br>
                                            <?php echo htmlspecialchars($order['event_tanggal'] . ' ' . $order['waktu']); ?>
                                        </small>
                                    </td>
                                    <td><?php echo htmlspecialchars($order['tanggal']); ?></td>
                                    <td><?php echo htmlspecialchars($order['metode']); ?></td>
                                    <td>Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#ticketModal<?php echo $order['id']; ?>">
                                            View Ticket
                                        </button>
                                    </td>
                                </tr>

                                <!-- Ticket Modal for each order -->
                                <div class="modal fade" id="ticketModal<?php echo $order['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-body p-0">
                                                <div class="ticket">
                                                    <div class="ticket-header">
                                                        <h2><?php echo htmlspecialchars($order['event_nama']); ?></h2>
                                                    </div>
                                                    <div class="ticket-body">
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <div class="ticket-info">
                                                                    <h5>Event Details</h5>
                                                                    <p><strong>Date:</strong> <?php echo htmlspecialchars($order['event_tanggal']); ?></p>
                                                                    <p><strong>Time:</strong> <?php echo htmlspecialchars($order['waktu']); ?></p>
                                                                    <p><strong>Location:</strong> <?php echo htmlspecialchars($order['lokasi']); ?></p>
                                                                </div>
                                                                <div class="ticket-info">
                                                                    <h5>Attendee Information</h5>
                                                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['nama']); ?></p>
                                                                    <p><strong>NPM:</strong> <?php echo htmlspecialchars($order['npm']); ?></p>
                                                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 text-center">
                                                                <div id="qrcode<?php echo $order['id']; ?>" class="mb-3"></div>
                                                            </div>
                                                        </div>
                                                        <div class="diagonal-line"></div>
                                                        <div class="ticket-number">
                                                            Ticket #<?php echo htmlspecialchars($order['kode_tiket']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <a href="generate_ticket.php?ticket_id=<?php echo $order['id_tiket']; ?>" class="btn btn-primary">
                                                    Download Ticket
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="mt-4">
                <a href="history.php?reset=1" class="btn btn-secondary">Check Another Email</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        // Generate QR Code for each ticket modal
        document.querySelectorAll('[id^="ticketModal"]').forEach(modal => {
            const orderId = modal.id.replace('ticketModal', '');
            const qrContainer = document.getElementById('qrcode' + orderId);
            const ticketCode = '<?php echo $order['kode_tiket']; ?>'; // Get the ticket code

            if (qrContainer) {
                new QRCode(qrContainer, {
                    text: ticketCode, // Use ticket code instead of order ID
                    width: 150,
                    height: 150,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }
        });
    </script>
</body>

</html>