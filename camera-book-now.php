<?php 
session_start();
include('includes/config.php');
error_reporting(0);

if (isset($_POST['submit'])) {
    $fromdate   = $_POST['fromdate'];
    $todate     = $_POST['todate']; 
    $message    = $_POST['message'];
    $useremail  = $_SESSION['login'];
    $status     = 'Pending Approval';
    $vhid       = intval($_GET['vhid']);
    $bookingno  = mt_rand(100000000, 999999999);
    $totalprice = $_POST['totalprice'];  // Total price from the hidden field

    // Check if the vehicle is available for the selected dates
    $ret = "
      SELECT 1 
      FROM tblbooking 
      WHERE VehicleId = :vhid
        AND status != 'Cancelled'
        AND (
            (:fromdate BETWEEN FromDate AND ToDate)
         OR (:todate   BETWEEN FromDate AND ToDate)
         OR (FromDate  BETWEEN :fromdate AND :todate)
        )";
    $query1 = $dbh->prepare($ret);
    $query1->bindParam(':vhid',     $vhid,     PDO::PARAM_INT);
    $query1->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
    $query1->bindParam(':todate',   $todate,   PDO::PARAM_STR);
    $query1->execute();

    if ($query1->rowCount() == 0) {
        // Insert booking information
        $sql = "
          INSERT INTO tblbooking
            (BookingNumber, userEmail, VehicleId, FromDate, ToDate, message, status, totalPrice)
          VALUES
            (:bookingno, :useremail, :vhid, :fromdate, :todate, :message, :status, :totalprice)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':bookingno', $bookingno, PDO::PARAM_STR);
        $query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
        $query->bindParam(':vhid',      $vhid,      PDO::PARAM_INT);
        $query->bindParam(':fromdate',  $fromdate,  PDO::PARAM_STR);
        $query->bindParam(':todate',    $todate,    PDO::PARAM_STR);
        $query->bindParam(':message',   $message,   PDO::PARAM_STR);
        $query->bindParam(':status',    $status,    PDO::PARAM_STR);
        $query->bindParam(':totalprice', $totalprice, PDO::PARAM_INT);  // Bind total price
        $query->execute();
        $lastInsertId = $dbh->lastInsertId();

        if ($lastInsertId) {
            echo "<script>alert('Booking successful — status: Pending Approval.');</script>";
            echo "<script>window.location.href='my-booking.php';</script>";
            exit;
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
            echo "<script>window.location.href='camera-listing.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Camera already booked for these dates.');</script>"; 
        echo "<script>window.location.href='camera-listing.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Snappy Boys | Camera Details</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="assets/css/style.css" type="text/css">
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>

<?php include('includes/header.php'); ?>

<?php 
$vhid = intval($_GET['vhid']);
$sql = "SELECT tblcameras.*, tblbrands.BrandName, tblbrands.id as bid FROM tblcameras 
        JOIN tblbrands ON tblbrands.id = tblcameras.VehiclesBrand 
        WHERE tblcameras.id = :vhid";
$query = $dbh->prepare($sql);
$query->bindParam(':vhid', $vhid, PDO::PARAM_STR);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
if ($query->rowCount() > 0) {
    foreach ($results as $result) {
        $_SESSION['brndid'] = $result->bid;
        $pricePerDay = $result->PricePerDay;
?>

<section id="listing_img_slider">
    <div><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1); ?>" class="img-responsive" alt="image" width="900" height="560"></div>
</section>

<section class="listing-detail">
    <div class="container">
        <div class="listing_detail_head row">
            <div class="col-md-9">
                <h2><?php echo htmlentities($result->BrandName); ?> , <?php echo htmlentities($result->VehiclesTitle); ?></h2>
            </div>
            <div class="col-md-3">
                <div class="price_info">
                    <p>₹<?php echo htmlentities($pricePerDay); ?> </p>Per Day
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <div class="camera_book_form">
                    <h5>Book Camera</h5>
                    <form name="book" method="post">
                        <div class="form-group">
                            <label for="fromdate">From Date</label>
                            <input type="date" class="form-control" name="fromdate" id="fromdate" required>
                        </div>
                        <div class="form-group">
                            <label for="todate">To Date</label>
                            <input type="date" class="form-control" name="todate" id="todate" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea class="form-control" name="message" placeholder="Your Message" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Total Price:</label>
                            <p id="totalPrice" style="font-weight:bold;">₹0</p>
                        </div>
                        <!-- Hidden field for total price -->
                        <input type="hidden" name="totalprice" id="totalprice">
                        <button type="submit" name="submit" class="btn btn-primary">Book Now</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// JS to calculate total price
document.addEventListener('DOMContentLoaded', function () {
    const fromInput = document.getElementById('fromdate');
    const toInput = document.getElementById('todate');
    const totalPriceEl = document.getElementById('totalPrice');
    const totalInput = document.getElementById('totalprice');
    const pricePerDay = <?php echo json_encode($pricePerDay); ?>;

    function updateTotal() {
        const fromDate = new Date(fromInput.value);
        const toDate = new Date(toInput.value);
        if (fromInput.value && toInput.value && toDate >= fromDate) {
            const diffTime = toDate.getTime() - fromDate.getTime();
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
            const total = diffDays * pricePerDay;
            totalPriceEl.textContent = "₹" + total;
            totalInput.value = total; // Store total price in hidden input
        } else {
            totalPriceEl.textContent = "₹0";
            totalInput.value = 0;  // Reset hidden input
        }
    }

    fromInput.addEventListener('change', updateTotal);
    toInput.addEventListener('change', updateTotal);
});
</script>

<?php } } ?>

<?php include('includes/footer.php'); ?>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/interface.js"></script> 
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
<script src="assets/switcher/js/switcher.js"></script>
<script src="assets/js/bootstrap-slider.min.js"></script> 
<script src="assets/js/slick.min.js"></script> 
<script src="assets/js/owl.carousel.min.js"></script>
</body>
</html>
