<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
} else {
    if(isset($_GET['del'])) {
        $id = $_GET['del'];
        $sql = "DELETE FROM tblbrands WHERE id = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $query->execute();
        $msg = "Page data updated successfully";
    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Registered Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSS Files -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .table-container {
            max-width: 95%;
            margin: 20px auto;
        }
        .panel {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .panel-heading {
            background-color: #3498db !important;
            color: #f8f9fa !important;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 15px;
            border-radius: 10px 10px 0 0;
        }
        .page-title {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 30px;
            font-weight: bold;
            color: #2c3e50;
        }
        .table thead {
            background-color: #2980b9;
            color: white;
        }
        .table tbody tr:hover {
            background-color:rgb(255, 255, 255);
            font-weight: bold;
        }
        .succWrap, .errorWrap {
            padding: 10px;
            margin: 10px auto;
            max-width: 95%;
            background: #fff;
            border-left: 5px solid;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .succWrap { border-color: #5cb85c; color: green; }
        .errorWrap { border-color: #d9534f; color: red; }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="ts-main-content">
        <?php include('includes/leftbar.php'); ?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <h2 class="page-title">Registered Users</h2>
                <div class="panel panel-default table-container">
                    <div class="panel-heading">User Details</div>
                    <div class="panel-body">
                        <?php if($error){ ?>
                            <div class="errorWrap"><strong>ERROR</strong>: <?php echo htmlentities($error); ?></div>
                        <?php } else if($msg){ ?>
                            <div class="succWrap"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?></div>
                        <?php } ?>

                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact No</th>
                                    <th>DOB</th>
                                    <th>Address</th>
                                    <th>City</th>
                                    <th>Country</th>
                                    <th>Reg Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $sql = "SELECT * FROM tblusers";
                                $query = $dbh->prepare($sql);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                $cnt = 1;
                                if($query->rowCount() > 0) {
                                    foreach($results as $result) { ?>
                                        <tr>
                                            <td><?php echo htmlentities($cnt); ?></td>
                                            <td><?php echo htmlentities($result->FullName); ?></td>
                                            <td><?php echo htmlentities($result->EmailId); ?></td>
                                            <td><?php echo htmlentities($result->ContactNo); ?></td>
                                            <td><?php echo htmlentities($result->dob); ?></td>
                                            <td><?php echo htmlentities($result->Address); ?></td>
                                            <td><?php echo htmlentities($result->City); ?></td>
                                            <td><?php echo htmlentities($result->Country); ?></td>
                                            <td><?php echo htmlentities($result->RegDate); ?></td>
                                        </tr>
                                <?php $cnt++; } } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
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
