<?php 
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin']) == 0) {   
    header('location:index.php');
} else {
    // Fetch pending bookings with user email and total price
    $sql = "
    SELECT 
        b.id, 
        u.EmailId AS UserEmail,  -- Fetching user email instead of full name
        b.BookingNumber AS BookingNo, 
        CONCAT(br.BrandName, ' ', c.VehiclesTitle) AS Camera, 
        b.FromDate, 
        b.ToDate, 
        b.status AS Status, 
        ((DATEDIFF(b.ToDate, b.FromDate) + 1) * c.PricePerDay) AS totalPrice, 
        b.PostingDate 
    FROM tblbooking b 
    JOIN tblusers u ON u.EmailId = b.userEmail 
    JOIN tblcameras c ON c.id = b.VehicleId 
    JOIN tblbrands br ON br.id = c.VehiclesBrand 
    WHERE b.status IN ('Pending Approval', 'Awaiting Payment')
    ORDER BY b.PostingDate DESC
    ";

    $query = $dbh->prepare($sql);
    $query->execute();
    $bookings = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Snappy Boys Portal | New Bookings</title>

    <!-- Font awesome -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .table-container {
            max-width: 90%;
            margin: 0 auto;
        }
        .panel {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .panel-heading {
            background-color: #2980b9 !important;
            color: white !important;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 10px 10px 0 0;
        }
        .table thead {
            background-color: #2980b9 !important;
            color: white !important;
            font-size: 16px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .table tbody tr:nth-child(odd) {
            background-color: #ffffff !important;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2 !important;
        }
        .table-hover tbody tr:hover {
            background-color: #d6e9f9 !important;
            font-weight: bold;
        }
        .btn-info {
            font-weight: bold;
            font-size: 14px;
        }
        .action-buttons {
            text-align: center;
            margin-top: 20px;
        }
      
        .btn:hover {
            transform: scale(1.05);
        }
        .btn-success {
            background-color: #28a745;
            border: none;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .alert {
            text-align: center;
            font-size: 18px;
            padding: 20px;
            border-radius: 8px;
            background-color: #ffeb3b;
            color: #d9534f;
            font-weight: bold;
        }
        .back-btn {
            margin-top: 20px;
            text-align: center;
        }
        .back-btn a {
            color: #2980b9;
            font-size: 18px;
            text-decoration: none;
            border: 2px solid #2980b9;
            padding: 10px 20px;
            border-radius: 30px;
            transition: all 0.3s ease-in-out;
        }
        .back-btn a:hover {
            background-color: #2980b9;
            color: white;
        }
    </style>
</head>

<body>
    <?php include('includes/header.php'); ?>
    <div class="ts-main-content">
        <?php include('includes/leftbar.php'); ?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="page-title text-center">New Camera Bookings</h2>
                        
                        <?php if (count($bookings) > 0): ?>
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>User Email</th>  <!-- Displaying user email -->
                                        <th>Booking No.</th>
                                        <th>Camera</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Status</th>
                                        <th>Total Price</th>
                                        <th>Posting Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $i => $b): ?>
                                    <tr>
                                        <td><?php echo $i + 1; ?></td>
                                        <td><?php echo htmlentities($b->UserEmail); ?></td>  <!-- Displaying user email -->
                                        <td><?php echo htmlentities($b->BookingNo); ?></td>
                                        <td><?php echo htmlentities($b->Camera); ?></td>
                                        <td><?php echo htmlentities($b->FromDate); ?></td>
                                        <td><?php echo htmlentities($b->ToDate); ?></td>
                                        <td><?php echo htmlentities($b->Status); ?></td>
                                        <td>₹<?php echo number_format($b->totalPrice, 2); ?></td>
                                        <td><?php echo htmlentities($b->PostingDate); ?></td>
                                        <td>
                                        <a href="camera-pay-link.php?bid=<?php echo $b->id; ?>" class="btn btn-success btn-sm d-inline-block" onclick="return confirm('Confirm this booking?');">Confirm</a>
<a href="canceled-bookings.php?bid=<?php echo $b->id; ?>" class="btn btn-danger btn-sm d-inline-block" onclick="return confirm('Cancel this booking?');">Cancel</a>

</td>

                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info">No pending bookings at the moment.</div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function confirmBooking() {
            if (confirm("Are you sure you want to confirm this booking?")) {
                window.location.href = "camera-pay-link.php?bid=<?php echo $booking->id; ?>";
            }
        }
        function cancelBooking() {
            if (confirm("Are you sure you want to cancel this booking?")) {
                window.location.href = "canceled-bookings.php?bid=<?php echo $booking->id; ?>";
            }
        }
    </script>
    <!-- Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>

<?php } ?>
