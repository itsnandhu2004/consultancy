<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{   
    header('location:index.php');
}
else{
    if(isset($_GET['del']))
    {
        $id=$_GET['del'];
        $sql = "DELETE FROM tblbrands WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $query->execute();
        $msg="Brand deleted successfully";
    }
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Car Rental Portal | Admin Manage Brands</title>

    <!-- Font awesome -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="css/style.css">
    <style>
       body {
        background-color: #f8f9fa;
        font-family: 'Arial', sans-serif;
    }
	.table-container {
    max-width: 70%; /* Adjust width as needed */
    margin: 0 auto; /* Centers the table */
}

table {
    width: 100%; /* Ensures table fills the container */
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
        background-color:rgb(255, 255, 255) !important; /* Light blue for alternate rows */
    }

    .table tbody tr:nth-child(even) {
        background-color: #ffffff !important;
    }

    .table-hover tbody tr:hover {
        background-color: #d6e9f9 !important;
        font-weight: bold;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        font-weight: bold;
        font-size: 14px;
    }

    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        font-weight: bold;
        font-size: 14px;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .errorWrap, .succWrap {
        padding: 10px;
        margin: 10px 0;
        background: #fff;
        border-left: 4px solid;
        box-shadow: 0 1px 1px rgba(0, 0, 0, .1);
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
                        <h2 class="page-title text-center">Manage Brands</h2>
                        <div class="panel panel-default  table-container">
                            <div class="panel-heading">Listed Brands</div>
                            <div class="panel-body">
                                <?php if($msg){?><div class="succWrap"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?> </div><?php } ?>
                                <table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Brand Name</th>
                                            <th>Creation Date</th>
                                            <th>Updation Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql = "SELECT * FROM tblbrands";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt=1;
                                        if($query->rowCount() > 0)
                                        {
                                            foreach($results as $result)
                                            { ?>
                                                <tr>
                                                    <td><?php echo htmlentities($cnt);?></td>
                                                    <td><?php echo htmlentities($result->BrandName);?></td>
                                                    <td><?php echo htmlentities($result->CreationDate);?></td>
                                                    <td><?php echo htmlentities($result->UpdationDate);?></td>
                                                    <td>
                                                        <a href="edit-brand.php?id=<?php echo $result->id;?>" class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a>
                                                        <a href="manage-brands.php?del=<?php echo $result->id;?>" onclick="return confirm('Do you want to delete?');" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            <?php $cnt=$cnt+1; }} ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#zctb').DataTable({
                "paging": false,       // Disables pagination
                "info": false,         // Hides "Showing X to Y of Z entries"
                "searching": false,     // Hides the search bar
                "ordering": false       // Disables sorting
            });
        });
    </script>

	<!-- Loading Scripts -->
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