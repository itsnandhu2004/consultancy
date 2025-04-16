<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
} else {
    if(isset($_GET['del'])) {
        $delid=intval($_GET['del']);
        $sql = "DELETE FROM tblcameras WHERE id=:delid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':delid', $delid, PDO::PARAM_STR);
        $query->execute();
        $msg = "Camera record deleted successfully";
    }
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Snappy Boys Portal | Admin Manage Cameras</title>

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
            max-width: 80%;
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
        .btn-danger, .btn-success {
            font-weight: bold;
            font-size: 14px;
        }
        .errorWrap, .succWrap {
            padding: 10px;
            margin: 10px 0;
            background: #fff;
            border-left: 4px solid;
            font-weight: bold;
        }
        .errorWrap { border-color: #dc3545; color: #dc3545; }
        .succWrap { border-color: #28a745; color: #28a745; }
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
                        <h2 class="page-title text-center">Manage Cameras</h2>
                        <div class="panel panel-default table-container">
                            <div class="panel-heading">Camera Details</div>
                            <div class="panel-body">
                                <?php if($msg){?><div class="succWrap"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?> </div><?php } ?>
                                <table id="cameraTable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Camera Model</th>
                                            <th>Brand</th>
                                            <th>Price Per Day</th>
                                            <th>Lens Type</th>
                                            <th>Model Year</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql = "SELECT tblcameras.VehiclesTitle, tblbrands.BrandName, tblcameras.PricePerDay, tblcameras.FuelType, tblcameras.ModelYear, tblcameras.id FROM tblcameras JOIN tblbrands ON tblbrands.id = tblcameras.VehiclesBrand";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt=1;
                                        if($query->rowCount() > 0) {
                                            foreach($results as $result) { ?>
                                                <tr>
                                                    <td><?php echo htmlentities($cnt);?></td>
                                                    <td><?php echo htmlentities($result->VehiclesTitle);?></td>
                                                    <td><?php echo htmlentities($result->BrandName);?></td>
                                                    <td><?php echo htmlentities($result->PricePerDay);?></td>
                                                    <td><?php echo htmlentities($result->FuelType);?></td>
                                                    <td><?php echo htmlentities($result->ModelYear);?></td>
                                                    <td>
                                                        <a href="edit-camera.php?id=<?php echo $result->id;?>" class="btn btn-success btn-sm">Edit</a>
                                                        <a href="manage-vehicles.php?del=<?php echo $result->id;?>" class="btn btn-danger btn-sm" onclick="return confirm('Do you want to delete?');">Delete</a>
                                                    </td>
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

    <!-- Loading Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#cameraTable').DataTable({
                "paging": false,          // Disables pagination
                "info": false,            // Hides the "Showing X to Y of Z entries"
                "searching": false,       // Disables the search box
                "ordering": false         // Disables column sorting
            });
        });
    </script>
		<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>
</body>
</html>
<?php } ?>