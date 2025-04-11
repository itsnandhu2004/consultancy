<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer
include('includes/config.php'); // Database connection

if(isset($_POST['email'])) {
    $email = trim($_POST['email']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address!";
        exit;
    }

    // Check if email is already registered
    $stmt = $dbh->prepare("SELECT EmailId FROM tblusers WHERE EmailId = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "This email is already registered. Please try logging in.";
        exit;
    }

    // Generate 6-digit OTP
    $otp = rand(100000, 999999);

    // Store OTP and email in session
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_email'] = $email;

    // PHPMailer setup
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'nandhini.info2004@gmail.com'; // Your email
        $mail->Password = 'hvyv inhm gyfc tqxj'; // Your app password (enable 2FA & App Passwords)
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'Snappy Boys');
        $mail->addAddress($email);

        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "Your OTP code for registration is: $otp";

        if ($mail->send()) {
            echo "OTP sent successfully!";
        } else {
            echo "Failed to send OTP. Please try again.";
        }
    } catch (Exception $e) {
        echo "Error sending OTP: " . $mail->ErrorInfo;
    }
} else {
    echo "No email provided!";
}
?>
