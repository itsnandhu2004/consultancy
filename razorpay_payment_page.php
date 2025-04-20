<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('includes/config.php');
require 'vendor/autoload.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$order_id = $_GET['order_id'] ?? '';

if (empty($order_id)) {
    die("Order ID missing.");
}

try {
    $api_key = 'rzp_test_X8TTnfXWnnhBBJ';
    $api_secret = 'ZXuwXQNcIk943UYRsLB2oeRp';

    $razorpay = new Api($api_key, $api_secret);
    $order = $razorpay->order->fetch($order_id);
    $amount = $order->amount;
    $currency = $order->currency;
} catch (Exception $e) {
    die("Error fetching order: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complete Your Payment</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <h2>Complete Your Payment</h2>

    <form action="event_payment_success.php" method="POST">
        <input type="hidden" name="razorpay_order_id" value="<?php echo htmlspecialchars($order_id); ?>">
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="razorpay_signature" id="razorpay_signature">
        <button type="button" id="pay_button">Pay Now</button>
    </form>

    <script>
        var options = {
            "key": "rzp_test_X8TTnfXWnnhBBJ",
            "amount": "<?php echo $amount; ?>", // dynamic amount
            "currency": "<?php echo $currency; ?>",
            "name": "Snappy Boys Portal",
            "description": "Event Booking Payment",
            "image": "https://yourwebsite.com/logo.png", // optional logo
            "order_id": "<?php echo $order_id; ?>",
            "handler": function (response) {
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                document.getElementById('razorpay_signature').value = response.razorpay_signature;
                document.forms[0].submit();
            },
            "prefill": {
                "name": "",
                "email": "",
                "contact": ""
            },
            "theme": {
                "color": "#3399cc"
            }
        };

        var rzp1 = new Razorpay(options);
        document.getElementById('pay_button').onclick = function (e) {
            rzp1.open();
            e.preventDefault();
        };
    </script>
</body>
</html>
