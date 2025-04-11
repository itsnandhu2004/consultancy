<?php
session_start();

if (isset($_POST['otp'])) {
    $user_otp = $_POST['otp'];
    
    if ($_SESSION['otp'] == $user_otp) {
        echo "<script>
                alert('OTP verified successfully!');
                window.location.href = 'index.php'; // Redirect to the next page after successful verification
              </script>";
    } else {
        echo "<script>
                alert('Invalid OTP! Please try again.');
                window.location.href = 'index.php'; // Redirect to index.php
              </script>";
    }
} else {
    echo "<script>
            alert('No OTP provided!');
            window.location.href = 'index.php';
          </script>";
}
?>
