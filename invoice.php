<?php
// Database connection
$conn = new mysqli('localhost:3305', 'root', '', 'ugtix');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get order ID from URL
$order_id = $_GET['id'];

// Fetch order details with event information using JOIN
$sql = "SELECT p.id, p.email, p.nama, p.npm, p.tanggal, p.metode, p.total, p.id_tiket,
               e.nama as nama_event, e.tanggal as event_tanggal, e.waktu, e.lokasi, e.harga, e.deskripsi 
        FROM pesanan p 
        JOIN events e ON p.id_event = e.id 
        WHERE p.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

// Close database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a1464;
            --accent-color: #9eff00;
            --dark-bg: #1a1a1a;
        }

        body {
            background-color: #f8f9fa;
        }

        .card {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 15px;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
        }

        .card-title {
            margin-bottom: 0;
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #13104d;
            border-color: #13104d;
        }

        .btn-accent {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: var(--dark-bg);
            font-weight: 600;
        }

        .btn-accent:hover {
            background-color: #8cdf00;
            border-color: #8cdf00;
            color: var(--dark-bg);
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            color: var(--primary-color);
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Invoice #<?php echo $order_id; ?></h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <h6 class="mb-3">Customer Details:</h6>
                        <div><strong>Name:</strong> <?php echo $order['nama']; ?></div>
                        <div><strong>Email:</strong> <?php echo $order['email']; ?></div>
                        <div><strong>NPM:</strong> <?php echo $order['npm']; ?></div>
                    </div>
                    <div class="col-sm-6">
                        <h6 class="mb-3">Event Details:</h6>
                        <div><strong>Event Name:</strong> <?php echo $order['nama_event']; ?></div>
                        <div><strong>Date:</strong> <?php echo $order['event_tanggal']; ?></div>
                        <div><strong>Time:</strong> <?php echo $order['waktu']; ?></div>
                        <div><strong>Location:</strong> <?php echo $order['lokasi']; ?></div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $order['nama_event']; ?></td>
                                <td>Rp <?php echo number_format($order['harga'], 0, ',', '.'); ?></td>
                                <td>Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-lg-4 col-sm-5 ms-auto">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td><strong>Total Amount</strong></td>
                                    <td>Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="text-center">
                            <a href="generate_ticket.php?ticket_id=<?php echo $order['id_tiket']; ?>" class="btn btn-accent me-2">
                                <i class="fas fa-ticket-alt me-2"></i>Generate Ticket
                            </a>
                            <button onclick="window.print()" class="btn btn-primary me-2">
                                <i class="fas fa-print me-2"></i>Print Invoice
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-home me-2"></i>Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</body>

</html>