<?php
session_start();
require_once('includes/config.php');
require 'vendor/autoload.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $razorpayPaymentId = $_POST['razorpay_payment_id'];
    $razorpayOrderId = $_POST['razorpay_order_id'];
    $razorpaySignature = $_POST['razorpay_signature'];

    // Initialize Razorpay API
    $api = new Api('rzp_test_X8TTnfXWnnhBBJ', 'ZXuwXQNcIk943UYRsLB2oeRp');

    try {
        // Step 1: Verify payment signature
        $attributes = [
            'razorpay_order_id' => $razorpayOrderId,
            'razorpay_payment_id' => $razorpayPaymentId,
            'razorpay_signature' => $razorpaySignature
        ];
        $api->utility->verifyPaymentSignature($attributes);

        // Step 2: Fetch payment amount
        $payment = $api->payment->fetch($razorpayPaymentId);
        $amountPaid = $payment->amount / 100; // Convert from paise to rupees

        // Step 3: Update booking in DB
        $sql = "UPDATE tblbooking 
                SET status = 'Paid and Confirmed',
                    payment_id = :paymentId, 
                    amount_paid = :amountPaid 
                WHERE razorpay_order_id = :orderId";

        $query = $dbh->prepare($sql);
        $query->bindParam(':paymentId', $razorpayPaymentId);
        $query->bindParam(':amountPaid', $amountPaid);
        $query->bindParam(':orderId', $razorpayOrderId);

        // Log the values being updated
        error_log("Updating tblbooking: OrderID = $razorpayOrderId, PaymentID = $razorpayPaymentId, Amount = $amountPaid");

        if ($query->execute()) {
            echo "<h2 style='color:green; text-align:center; margin-top:50px;'>✅ Payment Successful!</h2>";
            echo "<p style='text-align:center;'>
                    Order ID: <strong>$razorpayOrderId</strong><br>
                    Payment ID: <strong>$razorpayPaymentId</strong><br>
                    Amount Paid: <strong>₹$amountPaid</strong>
                  </p>";
        } else {
            echo "<h2 style='color:red; text-align:center; margin-top:50px;'>❌ Payment Verified, but failed to update status.</h2>";
        }

    } catch (SignatureVerificationError $e) {
        echo "<h2 style='color:red; text-align:center; margin-top:50px;'>❌ Payment verification failed!</h2>";
        echo "<p style='text-align:center;'>Error: " . $e->getMessage() . "</p>";
        error_log("SignatureVerificationError: " . $e->getMessage());
    } catch (Exception $ex) {
        echo "<h2 style='color:red; text-align:center; margin-top:50px;'>❌ An unexpected error occurred!</h2>";
        echo "<p style='text-align:center;'>Error: " . $ex->getMessage() . "</p>";
        error_log("Exception: " . $ex->getMessage());
    }
} else {
    echo "<h2 style='color:red; text-align:center; margin-top:50px;'>Invalid request</h2>";
}
?>
