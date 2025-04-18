
<?php  
session_start(); 
error_reporting(0); 
include('includes/config.php');  

if(strlen($_SESSION['alogin']) == 0) {        
    header('location:index.php'); 
    exit();
} else {     
    // Fetch booking details based on booking ID (use bid instead of id)
    if (isset($_GET['bid'])) {
        $sql = "     
            SELECT          
                b.id,          
                u.FullName,          
                u.EmailId AS UserEmail,          
                b.BookingNumber AS BookingNo,          
                CONCAT(br.BrandName, ' ', c.VehiclesTitle) AS Camera,          
                b.FromDate,          
                b.ToDate,          
                b.status AS Status,          
                ((DATEDIFF(b.ToDate, b.FromDate) + 1) * c.PricePerDay) AS totalPrice,          
                b.PostingDate,          
                b.userEmail      
            FROM tblbooking b      
            JOIN tblusers u ON u.EmailId = b.userEmail      
            JOIN tblcameras c ON c.id = b.VehicleId      
            JOIN tblbrands br ON br.id = c.VehiclesBrand      
            WHERE b.id = :bookingId      
            ORDER BY b.PostingDate DESC     
        ";      
        $query = $dbh->prepare($sql);     
        $query->bindParam(':bookingId', $_GET['bid'], PDO::PARAM_INT);     
        $query->execute();     
        $booking = $query->fetch(PDO::FETCH_OBJ); 
    } else {
        // If bid parameter is not found
        $booking = null;
    }
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
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
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
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
            margin-top: 50px;
        }
        .container-fluid {
            max-width: 960px;
        }
        .panel {
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .panel-heading {
            background-color: #2980b9;
            color: white;
            font-size: 22px;
            font-weight: bold;
            padding: 15px;
            border-radius: 12px 12px 0 0;
        }
        .table th {
            width: 30%;
        }
        .table td {
            padding: 10px;
        }
        .action-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            width: 200px;
            margin: 10px;
            border-radius: 30px;
            padding: 10px;
            font-size: 16px;
            transition: all 0.3s ease-in-out;
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
                        <h2 class="page-title text-center">Booking Details</h2>                         

                        <?php if($booking) { ?>                         
                            <div class="panel">                                 
                                <div class="panel-heading">Booking Information</div>                                 
                                <div class="panel-body">                                     
                                    <table class="table table-hover">                                         
                                        <tr><th>Booking Number</th><td><?php echo htmlentities($booking->BookingNo); ?></td></tr>                                         
                                        <tr><th>Camera</th><td><?php echo htmlentities($booking->Camera); ?></td></tr>                                         
                                        <tr><th>User Email</th><td><?php echo htmlentities($booking->UserEmail); ?></td></tr>                                         
                                        <tr><th>Booking Status</th><td><?php echo htmlentities($booking->Status); ?></td></tr>                                         
                                        <tr><th>From Date</th><td><?php echo htmlentities($booking->FromDate); ?></td></tr>                                         
                                        <tr><th>To Date</th><td><?php echo htmlentities($booking->ToDate); ?></td></tr>                                         
                                        <tr><th>Total Price</th><td>₹ <?php echo number_format($booking->totalPrice, 2); ?></td></tr>                                         
                                        <tr><th>Posting Date</th><td><?php echo htmlentities($booking->PostingDate); ?></td></tr>                                     
                                    </table>    
                                                                 
                                </div>                             
                            </div>                         
                        <?php } else { ?>                         
                            <p class="alert alert-danger text-center">Booking not found or invalid ID!</p>                         
                        <?php } ?>       
                    </div>   
                </div>
            </div>
        </div>
    </div>
     <!-- Scripts -->
     <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script> 
    <script src="assets/js/interface.js"></script> 

   

    <!-- Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>

<?php } ?>

