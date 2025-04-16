
<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (!isset($_SESSION['login'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

    $email = $_SESSION['login']; // Get the logged-in user email

    $sql = "SELECT * FROM bookings WHERE user_email = :email ORDER BY booking_date DESC";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    
    $query->execute();
    $bookings = $query->fetchAll(PDO::FETCH_OBJ);

?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Event Booking - My Booking History</title>
    <!--Bootstrap -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="assets/css/style.css" type="text/css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<!--Custome Style -->
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<!--OWL Carousel slider-->
<link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
<!--slick-slider -->
<link href="assets/css/slick.css" rel="stylesheet">
<!--bootstrap-slider -->
<link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
<!--FontAwesome Font Style -->
<link href="assets/css/font-awesome.min.css" rel="stylesheet">

<!-- SWITCHER -->
		<link rel="stylesheet" id="switcher-css" type="text/css" href="assets/switcher/css/switcher.css" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/red.css" title="red" media="all" data-default-color="true" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/orange.css" title="orange" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/blue.css" title="blue" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/pink.css" title="pink" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/green.css" title="green" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/purple.css" title="purple" media="all" />
        
<!-- Fav and touch icons -->
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/images/favicon-icon/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/images/favicon-icon/apple-touch-icon-114-precomposed.html">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/images/favicon-icon/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="assets/images/favicon-icon/apple-touch-icon-57-precomposed.png">
<link rel="shortcut icon" href="assets/images/favicon-icon/favicon.png">
<!-- Google-Font-->
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->  
</head>
<body>
    <!--Header-->
    <?php include('includes/header.php'); ?>
    <!-- /Header -->

    <!--Page Header-->
    <section class="page-header profile_page">
        <div class="container">
            <div class="page-header_wrap">
                <div class="page-heading">
                    <h1>My Booking</h1>
                </div>
                <ul class="coustom-breadcrumb">
                    <li><a href="index.php">Home</a></li>
                    <li>My Booking</li>
                </ul>
            </div>
        </div>
        <div class="dark-overlay"></div>
    </section>
    <!-- /Page Header -->

    <div class="container mt-5">
        <h2 class="text-center">My Booking History</h2>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Booking Date</th>
                        <th>Event Name</th>
                        <th>Date Range</th>
                        <th>Slot Details</th>
                        <th>Total Price (₹)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($bookings) > 0): ?>
                        <?php foreach ($bookings as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row->booking_date) ?></td>
                                <td><?= htmlspecialchars($row->event_name) ?></td>
                                <td><?= htmlspecialchars($row->date_range) ?></td>
                                <td>
                                    <?php
                                    $slotDetails = json_decode($row->slot_details, true);
                                    if (is_array($slotDetails)) {
                                        foreach ($slotDetails as $slot) {
                                            $date = isset($slot['date']) ? htmlspecialchars($slot['date']) : "N/A";
                                            $slotType = isset($slot['slot_type']) ? htmlspecialchars($slot['slot_type']) : "N/A";
                                            echo "📅 " . $date . " | ⏳ " . $slotType . "<br>";
                                        }
                                    } else {
                                        echo "N/A";
                                    }
                                    ?>
                                </td>
                                <td><?= number_format($row->total_price, 2) ?></td>
                                <td><span class='badge <?= getStatusClass($row->status) ?>'><?= htmlspecialchars($row->status) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No bookings found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!--Footer-->
    <?php include('includes/footer.php'); ?>
    <!-- /Footer -->
     <!-- Scripts --> 
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
<!--Switcher-->
<script src="assets/switcher/js/switcher.js"></script>
<!--bootstrap-slider-JS--> 
<script src="assets/js/bootstrap-slider.min.js"></script> 
<!--Slider-JS--> 
<script src="assets/js/slick.min.js"></script> 
<script src="assets/js/owl.carousel.min.js"></script>
</body>
</html>

<?php
// Function to add Bootstrap badge colors based on status
function getStatusClass($status) {
    switch ($status) {
        case 'Pending': return 'bg-warning text-dark';
        case 'Confirmed': return 'bg-success';
        case 'Canceled': return 'bg-danger';
        default: return 'bg-secondary';
    }
}
?>


















