<?php
session_start();
include('includes/config.php'); // Database connection

if (isset($_POST['signup'])) {
    $fname = $_POST['fullname'];
    $email = $_POST['emailid'];
    $mobile = $_POST['mobileno'];
    $password = md5($_POST['password']);
    $user_otp = $_POST['otp'];

    // Check if email already exists
    $checkEmail = $dbh->prepare("SELECT * FROM tblusers WHERE EmailId = :email");
    $checkEmail->bindParam(':email', $email, PDO::PARAM_STR);
    $checkEmail->execute();
    
    if ($checkEmail->rowCount() > 0) {
        echo "<script>alert('This email is already registered. Please try logging in.');</script>";
        exit;
    }

    // Check if OTP and email match the session values
    if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_email'])) {
        echo "<script>alert('OTP session expired. Please try again.');</script>";
        exit;
    }

    if ($_SESSION['otp'] != $user_otp || $_SESSION['otp_email'] != $email) {
        echo "<script>alert('Invalid OTP! Please try again.');</script>";
        exit;
    }

    // Insert user details into the database
    $sql = "INSERT INTO tblusers (FullName, EmailId, ContactNo, Password) VALUES (:fname, :email, :mobile, :password)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':fname', $fname, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();

    $lastInsertId = $dbh->lastInsertId();
    if ($lastInsertId) {
        echo "<script>alert('Registration successful. Now you can log in.');</script>";

        // Clear OTP session after successful registration
        unset($_SESSION['otp']);
        unset($_SESSION['otp_email']);
    } else {
        echo "<script>alert('Something went wrong. Please try again.');</script>";
    }
}
?>

<!-- Signup Form -->
<div class="modal fade" id="signupform">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        <h3 class="modal-title">Sign Up</h3>
      </div>
      <div class="modal-body">
        <form method="post" name="signup">
          <div class="form-group">
            <input type="text" class="form-control" name="fullname" placeholder="Full Name" required>
          </div>
          <div class="form-group">
            <input type="text" class="form-control" name="mobileno" placeholder="Mobile Number" maxlength="10" required>
          </div>
          <div class="form-group">
            <input type="email" class="form-control" name="emailid" id="emailid" placeholder="Email Address" required>
            <button type="button" onclick="sendOTP()" class="btn btn-primary">Send OTP</button>
            <span id="email-message" style="color: red;"></span>
            <span id="otp-message" style="font-size:12px;"></span>
          </div>
          <div class="form-group">
            <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter OTP" required>
          </div>
          <div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="Password" required>
          </div>
          <div class="form-group checkbox">
            <input type="checkbox" id="terms_agree" required>
            <label for="terms_agree">I Agree with <a href="#">Terms and Conditions</a></label>
          </div>
          <div class="form-group">
            <input type="submit" value="Sign Up" name="signup" class="btn btn-block">
          </div>
        </form>
      </div>
      <div class="modal-footer text-center">
        <p>Already have an account? <a href="#loginform" data-toggle="modal" data-dismiss="modal">Login Here</a></p>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript for Checking Email and Sending OTP -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $("#emailid").on("blur", function() {
        var email = $(this).val().trim();
        if (email !== "") {
            $.ajax({
                url: "verify_e.php",
                type: "POST",
                data: { email: email },
                success: function(response) {
                    $("#email-message").html(response);
                }
            });
        }
    });
});

function sendOTP() {
    var email = $("#emailid").val().trim();
    if (email === "") {
        $("#otp-message").html("<span style='color: red;'>Please enter an email address.</span>");
        return;
    }

    $.ajax({
        url: "send_otp.php",
        type: "POST",
        data: { email: email },
        beforeSend: function() {
            $("#otp-message").html("<span style='color: blue;'>Sending OTP...</span>");
        },
        success: function(response) {
            $("#otp-message").html("<span style='color: green;'>" + response + "</span>");
        },
        error: function() {
            $("#otp-message").html("<span style='color: red;'>Error sending OTP. Please try again.</span>");
        }
    });
}
</script>
