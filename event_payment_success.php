<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php'; // Composer autoload
include('includes/config.php'); // DB config

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

// Razorpay credentials
$api_key = 'rzp_test_X8TTnfXWnnhBBJ';
$api_secret = 'ZXuwXQNcIk943UYRsLB2oeRp';

// Get POST data
$razorpay_order_id = $_POST['razorpay_order_id'] ?? '';
$razorpay_payment_id = $_POST['razorpay_payment_id'] ?? '';
$razorpay_signature = $_POST['razorpay_signature'] ?? '';

if (!$razorpay_order_id || !$razorpay_payment_id || !$razorpay_signature) {
    die("Invalid Razorpay response.");
}

try {
    // Verify Razorpay signature
    $api = new Api($api_key, $api_secret);
    $attributes = [
        'razorpay_order_id' => $razorpay_order_id,
        'razorpay_payment_id' => $razorpay_payment_id,
        'razorpay_signature' => $razorpay_signature
    ];
    $api->utility->verifyPaymentSignature($attributes);

    // Fetch booking
    $sql = "SELECT * FROM event_bookings WHERE razorpay_order_id = :order_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':order_id', $razorpay_order_id, PDO::PARAM_STR);
    $query->execute();
    $booking = $query->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        die("Booking not found.");
    }

    // Update booking status
    $update = "UPDATE event_bookings SET razorpay_payment_id = :payment_id, status = 'Paid and Confirmed' WHERE razorpay_order_id = :order_id";
    $stmt = $dbh->prepare($update);
    $stmt->bindParam(':payment_id', $razorpay_payment_id, PDO::PARAM_STR);
    $stmt->bindParam(':order_id', $razorpay_order_id, PDO::PARAM_STR);
    $stmt->execute();

    // Extract booking info
    $event_name = htmlspecialchars($booking['event_name']);
    $event_dates = htmlspecialchars($booking['event_dates']);
    $total_price = htmlspecialchars($booking['total_price']);
    $status = 'Paid and Confirmed'; // Updated
    $user_email = htmlspecialchars($booking['user_email']);
    $booking_id = htmlspecialchars($booking['booking_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f7;
            margin: 0;
            padding: 20px;
        }
        #pdfContent {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: green;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: crimson;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .btn:hover {
            background: darkred;
        }
    </style>
</head>
<body>

<div id="pdfContent">
    <h2>Payment Successful!</h2>
    <p><strong>Order ID:</strong> <?= $razorpay_order_id ?></p>
    <p><strong>Payment ID:</strong> <?= $razorpay_payment_id ?></p>
    <p><strong>Booking ID:</strong> <?= $booking_id ?></p>
    <p><strong>Event:</strong> <?= $event_name ?></p>
    <p><strong>Event Date(s):</strong> <?= $event_dates ?></p>
    <p><strong>User Email:</strong> <?= $user_email ?></p>
    <p><strong>Status:</strong> <?= $status ?></p>
    <p><strong>Total Amount:</strong> ₹<?= $total_price ?></p>
</div>

<div style="text-align: center;">
    <button onclick="exportToPDF()" class="btn">Download Receipt as PDF</button>
</div>

<!-- jsPDF & html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
async function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF();

    const element = document.getElementById('pdfContent');

    await html2canvas(element, { scale: 2 }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const imgProps = pdf.getImageProperties(imgData);
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

        pdf.addImage(imgData, 'PNG', 10, 10, pdfWidth - 20, pdfHeight);
        pdf.save("payment_receipt.pdf");
    });
}
</script>

</body>
</html>

<?php
} catch (SignatureVerificationError $e) {
    die("Payment Signature Verification Failed: " . $e->getMessage());
} catch (Exception $e) {
    die("Payment Processing Failed: " . $e->getMessage());
}
?>
