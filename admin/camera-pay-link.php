<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('includes/config.php');
require '../vendor/autoload.php'; // Razorpay SDK & PHPMailer

use Razorpay\Api\Api;

if(strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

if(isset($_GET['bid'])) { // changed from 'id' to 'bid'
    $bookingId = $_GET['bid']; // updated variable assignment

    // Fetch booking details
    $sql = "SELECT b.id as bookingId, u.EmailId AS UserEmail, b.BookingNumber AS BookingNo,
            CONCAT(br.BrandName, ' ', c.VehiclesTitle) AS Camera,
            b.FromDate, b.ToDate, b.totalPrice
            FROM tblbooking b
            JOIN tblusers u ON u.EmailId = b.userEmail
            JOIN tblcameras c ON c.id = b.VehicleId
            JOIN tblbrands br ON br.id = c.VehiclesBrand
            WHERE b.id = :bookingId";
    
    $query = $dbh->prepare($sql);
    $query->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
    $query->execute();
    $booking = $query->fetch(PDO::FETCH_OBJ);

    if ($booking) {
        // Update status to 'Awaiting Payment'
        $updateStatusSql = "UPDATE tblbooking SET status = 'Awaiting Payment' WHERE id = :bookingId";
        $updateQuery = $dbh->prepare($updateStatusSql);
        $updateQuery->bindParam(':bookingId', $booking->bookingId, PDO::PARAM_INT);
        $updateQuery->execute();

        // ✅ Razorpay Order Generation
        $api = new Api('rzp_test_X8TTnfXWnnhBBJ', 'ZXuwXQNcIk943UYRsLB2oeRp');

        $amountInPaise = intval(floatval($booking->totalPrice) * 100);

        if ($amountInPaise < 100) {
            echo "<script type='text/javascript'>
                    alert('Booking amount is too low. Minimum payment must be ₹1.');
                    window.location.href = 'booking-details.php';
                  </script>";
            exit;
        }

        $orderData = [
            'receipt' => $booking->BookingNo,
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'payment_capture' => 1
        ];
        $order = $api->order->create($orderData);
        $razorpayOrderId = $order['id'];

        $saveOrderId = $dbh->prepare("UPDATE tblbooking SET razorpay_order_id = :oid WHERE id = :bookingId");
        $saveOrderId->bindParam(':oid', $razorpayOrderId);
        $saveOrderId->bindParam(':bookingId', $booking->bookingId);
        $saveOrderId->execute();

        // ✅ Send email with payment link
        $mail = new PHPMailer\PHPMailer\PHPMailer();

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'nandhini.info2004@gmail.com';
        $mail->Password = 'kprb pfov htxa curn';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('no-reply@snappyboys.com', 'Snappy Boys');
        $mail->addAddress($booking->UserEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Payment Link for Booking ID ' . $booking->BookingNo;

        $paymentLink = "http://localhost/camerarental/checkout.php?bookingId=" . $booking->BookingNo;

        $mail->Body = "
            Dear User,<br><br>
            Your booking with ID <strong>{$booking->BookingNo}</strong> for camera <strong>{$booking->Camera}</strong> has been initiated.<br><br>
            <strong>Total Price:</strong> ₹" . number_format($booking->totalPrice, 2) . "<br>
            <strong>From:</strong> {$booking->FromDate}<br>
            <strong>To:</strong> {$booking->ToDate}<br><br>
            Please complete your payment using the link below:<br>
            <a href='{$paymentLink}'>Click here to Pay</a><br><br>
            Regards,<br>Snappy Boys Team
        ";

        if ($mail->send()) {
            echo "<script type='text/javascript'>
                    alert('Booking confirmed. An email with the payment link has been sent to the user.');
                    window.location.href = 'new-bookings.php';
                  </script>";
        } else {
            echo "<script type='text/javascript'>
                    alert('Mailer Error: " . $mail->ErrorInfo . "');
                    window.location.href = 'new-bookings.php';
                  </script>";
        }
    } else {
        echo "<script type='text/javascript'>
                alert('Booking not found!');
                window.location.href = 'new-bookings.php';
              </script>";
    }
} else {
    echo "<script type='text/javascript'>
            alert('Invalid booking ID.');
            window.location.href = 'new-bookings.php';
          </script>";
}
?>
