<?php
// Database connection
require_once 'process/koneksi.php';

// Get ticket ID from URL
$ticket_id = $_GET['ticket_id'];

// Update SQL query to match table structure
$sql = "SELECT p.email, p.nama, p.npm, p.tanggal,
               e.nama as nama_event, e.tanggal as event_tanggal, e.waktu, e.lokasi,
               t.id as ticket_id, t.kode_tiket, t.status
        FROM tiket t
        JOIN pesanan p ON t.id_pesanan = p.id 
        JOIN events e ON t.id_event = e.id 
        WHERE t.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();

// Add error handling
if (!$ticket) {
    die("Ticket not found");
}

// Close database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Ticket #<?php echo $ticket['kode_tiket']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a1464;
            --accent-color: #9eff00;
        }

        body {
            background-color: #f8f9fa;
        }

        .ticket {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
            margin: 20px auto;
            max-width: 800px;
        }

        .ticket-header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .ticket-body {
            padding: 30px;
            position: relative;
        }

        .ticket-info {
            margin-bottom: 20px;
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

        @media print {
            .no-print {
                display: none;
            }

            body {
                background-color: white;
            }

            .ticket {
                box-shadow: none;
            }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <div class="ticket">
            <div class="ticket-header">
                <h2><?php echo $ticket['nama_event']; ?></h2>
            </div>
            <div class="ticket-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="ticket-info">
                            <h5>Event Details</h5>
                            <p><strong>Date:</strong> <?php echo $ticket['event_tanggal']; ?></p>
                            <p><strong>Time:</strong> <?php echo $ticket['waktu']; ?></p>
                            <p><strong>Location:</strong> <?php echo $ticket['lokasi']; ?></p>
                        </div>
                        <div class="ticket-info">
                            <h5>Attendee Information</h5>
                            <p><strong>Name:</strong> <?php echo $ticket['nama']; ?></p>
                            <p><strong>NPM:</strong> <?php echo $ticket['npm']; ?></p>
                            <p><strong>Email:</strong> <?php echo $ticket['email']; ?></p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div id="qrcode" class="mb-3"></div>
                    </div>
                </div>
                <div class="diagonal-line"></div>
                <div class="ticket-number">
                    Ticket #<?php echo $ticket['kode_tiket']; ?>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 no-print">
            <button onclick="generatePDF()" class="btn btn-primary me-2">
                <i class="fas fa-print me-2"></i>Print Ticket
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-home me-2"></i>Back to Home
            </a>
        </div>
    </div>

    <script>
        // Generate QR Code
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: "<?php echo $ticket['kode_tiket']; ?>",
            width: 150,
            height: 150,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        function generatePDF() {
            // Get the ticket element
            const ticket = document.querySelector('.ticket');

            // Configuration for html2pdf
            const opt = {
                margin: 1,
                filename: 'ticket-<?php echo $ticket['kode_tiket']; ?>.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: 'in',
                    format: 'letter',
                    orientation: 'portrait'
                }
            };

            // Generate PDF
            html2pdf().set(opt).from(ticket).save();
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</body>

</html>