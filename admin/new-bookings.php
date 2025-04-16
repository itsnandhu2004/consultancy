<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
} else {
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
    </style>
</head>

<body>
    <?php include('includes/header.php');?>
    <div class="ts-main-content">
        <?php include('includes/leftbar.php');?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="page-title text-center">New Camera Bookings</h2>
                        <div class="panel panel-default table-container">
                            <div class="panel-heading">Bookings Info</div>
                            <div class="panel-body">
                                <table id="bookingTable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Booking No.</th>
                                            <th>Camera</th>
                                            <th>From Date</th>
                                            <th>To Date</th>
                                            <th>Status</th>
                                            <th>Posting Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $status = 0;
                                        $sql = "SELECT tblusers.FullName, tblbrands.BrandName, tblcameras.VehiclesTitle, tblbooking.FromDate, tblbooking.ToDate, tblbooking.VehicleId as vid, tblbooking.Status, tblbooking.PostingDate, tblbooking.id, tblbooking.BookingNumber 
                                                FROM tblbooking 
                                                JOIN tblcameras ON tblcameras.id=tblbooking.VehicleId 
                                                JOIN tblusers ON tblusers.EmailId=tblbooking.userEmail 
                                                JOIN tblbrands ON tblcameras.VehiclesBrand=tblbrands.id 
                                                WHERE tblbooking.Status=:status";
                                        $query = $dbh->prepare($sql);
                                        $query->bindParam(':status', $status, PDO::PARAM_STR);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt = 1;
                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) { ?>
                                                <tr>
                                                    <td><?php echo htmlentities($cnt);?></td>
                                                    <td><?php echo htmlentities($result->FullName);?></td>
                                                    <td><?php echo htmlentities($result->BookingNumber);?></td>
                                                    <td><a href="edit-camera.php?id=<?php echo htmlentities($result->vid);?>">
                                                        <?php echo htmlentities($result->BrandName) . ' , ' . htmlentities($result->VehiclesTitle);?></a>
                                                    </td>
                                                    <td><?php echo htmlentities($result->FromDate);?></td>
                                                    <td><?php echo htmlentities($result->ToDate);?></td>
                                                    <td><?php 
                                                        if($result->Status == 0) echo 'Not Confirmed yet';
                                                        elseif($result->Status == 1) echo 'Confirmed';
                                                        else echo 'Cancelled';
                                                    ?></td>
                                                    <td><?php echo htmlentities($result->PostingDate);?></td>
                                                    <td><a href="bookig-details.php?bid=<?php echo htmlentities($result->id);?>"> View</a></td>
									
                                                </tr>
                                        <?php $cnt++; }} ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
<?php } ?>