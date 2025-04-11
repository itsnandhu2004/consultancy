

<?php
include('includes/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    $stmt = $dbh->prepare("SELECT EmailId FROM tblusers WHERE EmailId = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "<span style='color: red;'>This email is already registered. Try logging in.</span>";
    } else {
        echo "<span style='color: green;'>Email available. You can proceed with OTP.</span>";
    }
}
?>