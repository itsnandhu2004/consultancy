<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('includes/config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Razorpay\Api\Api;
// Include PHPMailer
require '../vendor/autoload.php';

// Handle Confirm and Cancel Actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['booking_id'];
    
    // Fetch booking details to get email and total price
    $sql = "SELECT * FROM event_bookings WHERE booking_id = :booking_id AND status = 'Pending Approval'";

    $query = $dbh->prepare($sql);
    $query->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
    $query->execute();
    $booking = $query->fetch(PDO::FETCH_ASSOC);

    // Check if booking exists
    if (!$booking) {
        echo "Booking not found or already processed.";
        exit;
    }

    // Handle confirm or cancel
    if (isset($_POST['confirm'])) {
        // Update status to "Awaiting Payment"
        $status = "Awaiting Payment";

        // Razorpay Payment Link Generation
        $api_key = 'rzp_test_X8TTnfXWnnhBBJ';
        $api_secret = 'ZXuwXQNcIk943UYRsLB2oeRp';

        // Create Razorpay Order
        $razorpay = new Razorpay\Api\Api($api_key, $api_secret);
        $orderData = [
            'receipt'         => $booking_id,
            'amount'          => $booking['total_price'] * 100,  // Price in paise
            'currency'        => 'INR',
            'payment_capture' => 1
        ];

        $order = $razorpay->order->create($orderData);
        $razorpay_order_id = $order->id;

        // Save Razorpay order ID in the database
        $sql = "UPDATE event_bookings SET razorpay_order_id = :razorpay_order_id, status = :status WHERE booking_id = :booking_id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':razorpay_order_id', $razorpay_order_id, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':booking_id', $booking_id, PDO::PARAM_INT); // This ensures only the selected booking is updated
        $query->execute();

        // Send payment link to user's email
        $paymentLink = "http://localhost/camerarental/razorpay_payment_page.php?order_id=" . $razorpay_order_id;

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send email
            $mail->SMTPAuth = true;
            $mail->Username = 'nandhini.info2004@gmail.com'; // SMTP username
            $mail->Password = 'kprb pfov htxa curn'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('nandhini.info2004@gmail.com', 'Snappy Boys Photography');
            $mail->addAddress($booking['user_email']); // User's email

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Payment Link for Your Event Booking';
            $mail->Body    = "Dear Customer,<br><br>Your booking for the event '{$booking['event_name']}' has been confirmed. Please complete your payment using the following link: <br><br><a href='{$paymentLink}'>Pay Now</a><br><br>Thank you for choosing Snappy Boys Portal.";

            // Send the email
            $mail->send();
            $_SESSION['email_sent'] = "Payment link has been sent to the user's email.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    } elseif (isset($_POST['cancel'])) {
        $status = "Cancelled";
        
        $sql = "UPDATE event_bookings SET status = :status WHERE booking_id = :booking_id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        $query->execute();
    
        $_SESSION['cancelled'] = "Booking has been cancelled.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    
}

// Fetch New Bookings
$sql = "
SELECT 
    eb.booking_id, 
    eb.event_name, 
    eb.user_email, 
    eb.event_dates, 
    eb.total_price, 
    eb.status, 
    eb.booked_date, 
    eb.razorpay_order_id, 
    eb.razorpay_payment_id
FROM event_bookings eb 
WHERE eb.status IN ('Pending Approval', 'Awaiting Payment') 
ORDER BY eb.booked_date DESC
";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Snappy Boys Portal | New Bookings</title>

    <!-- Font awesome -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .table-container {
            max-width: 90%;
            margin: 0 auto;
        }
        .panel {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .panel-heading {
            background-color: #2980b9 !important;
            color: white !important;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 10px 10px 0 0;
        }
        .table thead {
            background-color: #2980b9 !important;
            color: white !important;
            font-size: 16px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .table tbody tr:nth-child(odd) {
            background-color: #ffffff !important;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2 !important;
        }
        .table-hover tbody tr:hover {
            background-color: #d6e9f9 !important;
          
        }
    </style>
</head>

<body>
    <?php include('includes/header.php'); ?>
    <div class="ts-main-content">
        <?php include('includes/leftbar.php'); ?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="page-title text-center">New Event Bookings</h2>
                        <div class="panel panel-default table-container">
                            <div class="panel-heading">Bookings Info</div>
                            <div class="panel-body">
                                <table id="bookingTable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Event Name</th>
                                            <th>User Email</th>
                                            <th>Event Dates</th>
                                            <th>Total Price</th>
                                            <th>Booking Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php
$cnt = 1;
foreach ($results as $row) { ?>
    <form method="POST"> <!-- Each booking has its own form -->
        <tr>
            <td><?php echo htmlentities($cnt); ?></td>
            <td><?php echo htmlentities($row['event_name']); ?></td>
            <td><?php echo isset($row['user_email']) ? htmlentities($row['user_email']) : 'Not Available'; ?></td>
            <td><?php echo htmlentities($row['event_dates']); ?></td>
            <td><?php echo htmlentities($row['total_price']); ?></td>
            <td><?php echo htmlentities($row['booked_date']); ?></td>
            <td><?php echo isset($row['status']) ? htmlentities($row['status']) : 'Not Available'; ?></td>
            <td>
                <input type="hidden" name="booking_id" value="<?php echo htmlentities($row['booking_id']); ?>"> <!-- hidden booking_id -->
                <button type="submit" name="confirm" class="btn btn-success" onclick="return confirm('Are you sure you want to send the payment link to the customer?');">Confirm</button>
                <button type="submit" name="cancel" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel</button>
            </td>
        </tr>
    </form> <!-- Close the form for each row -->
<?php $cnt++; } ?>
</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if (isset($_SESSION['email_sent'])): ?>
        <script>
            alert("<?php echo $_SESSION['email_sent']; ?>");
        </script>
        <?php unset($_SESSION['email_sent']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['cancelled'])): ?>
    <script>
        alert("<?php echo $_SESSION['cancelled']; ?>");
    </script>
    <?php unset($_SESSION['cancelled']); ?>
<?php endif; ?>

    <script>
        function confirmSendPayment() {
            return confirm('Are you sure you want to send the payment link to the customer?');
        }
    </script>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>
</body>

</html>
