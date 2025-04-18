<?php
require_once('includes/config.php');
require 'vendor/autoload.php';

if (isset($_GET['bookingId'])) {
    $bookingId = $_GET['bookingId'];

    // Fetch booking details
    $sql = "SELECT b.BookingNumber, b.totalPrice, b.razorpay_order_id, u.FullName, u.EmailId 
            FROM tblbooking b
            JOIN tblusers u ON u.EmailId = b.userEmail
            WHERE b.BookingNumber = :bookingId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':bookingId', $bookingId, PDO::PARAM_STR);
    $query->execute();
    $booking = $query->fetch(PDO::FETCH_OBJ);

    if ($booking && !empty($booking->razorpay_order_id)) {
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Checkout - Snappy Boys</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <h2 style="text-align:center; margin-top: 30px;">Booking ID: <?php echo htmlentities($booking->BookingNumber); ?></h2>
    <p style="text-align:center;">Launching payment gateway...</p>

    <script>
        var options = {
            "key": "rzp_test_X8TTnfXWnnhBBJ", // Replace with your real Razorpay Key ID
            "amount": "<?php echo $booking->totalPrice * 100; ?>", // in paise
            "currency": "INR",
            "name": "Snappy Boys",
            "description": "Booking #<?php echo $booking->BookingNumber; ?>",
            "image": "https://example.com/logo.png",
            "order_id": "<?php echo $booking->razorpay_order_id; ?>",
            "handler": function (response){
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = 'payment-success.php';

                var fields = {
                    "razorpay_payment_id": response.razorpay_payment_id,
                    "razorpay_order_id": response.razorpay_order_id,
                    "razorpay_signature": response.razorpay_signature,
                    "bookingId": "<?php echo $booking->BookingNumber; ?>"
                };

                for (var key in fields) {
                    var input = document.createElement("input");
                    input.type = "hidden";
                    input.name = key;
                    input.value = fields[key];
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
            },
            "prefill": {
                "name": "<?php echo htmlentities($booking->FullName); ?>",
                "email": "<?php echo htmlentities($booking->EmailId); ?>",
                "contact": "9000000000"
            },
            "theme": {
                "color": "#F37254"
            }
        };

        var rzp1 = new Razorpay(options);
        rzp1.open();
    </script>
</body>
</html>
<?php
    } else {
        echo "<h3 style='text-align:center; color:red;'>Invalid booking or payment ID.</h3>";
    }
} else {
    echo "<h3 style='text-align:center; color:red;'>No booking ID provided.</h3>";
}
?>
