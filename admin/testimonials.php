
<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
} else {
if(isset($_REQUEST['eid'])) {
    $eid=intval($_GET['eid']);
    $status="0";
    $sql = "UPDATE tbltestimonial SET status=:status WHERE id=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->bindParam(':eid', $eid, PDO::PARAM_STR);
    $query->execute();
    $msg="Testimonial Successfully Inactive";
}

if(isset($_REQUEST['aeid'])) {
    $aeid=intval($_GET['aeid']);
    $status=1;
    $sql = "UPDATE tbltestimonial SET status=:status WHERE id=:aeid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->bindParam(':aeid', $aeid, PDO::PARAM_STR);
    $query->execute();
    $msg="Testimonial Successfully Active";
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Manage Testimonials</title>
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Arial', sans-serif; }
        .table-container { max-width: 90%; margin: 20px auto; }
        .panel { border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .panel-heading { background-color: #2980b9 !important; color: white !important; font-size: 20px; font-weight: bold; text-transform: uppercase; border-radius: 10px 10px 0 0; }
        .table thead { background-color: #2980b9 !important; color: white !important; }
        .table tbody tr:hover { background-color: #d6e9f9 !important; font-weight: bold; }
        .btn-action { font-weight: bold; font-size: 14px; }
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
                        <h2 class="page-title text-center">Manage Testimonials</h2>
                        <div class="panel panel-default table-container">
                            <div class="panel-heading">User Testimonials</div>
                            <div class="panel-body">
                                <?php if($msg){ ?><div class="alert alert-success"> <strong>SUCCESS:</strong> <?php echo htmlentities($msg); ?> </div><?php } ?>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Testimonial</th>
                                            <th>Posting Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql = "SELECT tblusers.FullName,tbltestimonial.UserEmail,tbltestimonial.Testimonial,tbltestimonial.PostingDate,tbltestimonial.status,tbltestimonial.id FROM tbltestimonial JOIN tblusers ON tblusers.Emailid=tbltestimonial.UserEmail";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt=1;
                                        if($query->rowCount() > 0) {
                                            foreach($results as $result) { ?>
                                                <tr>
                                                    <td><?php echo htmlentities($cnt);?></td>
                                                    <td><?php echo htmlentities($result->FullName);?></td>
                                                    <td><?php echo htmlentities($result->UserEmail);?></td>
                                                    <td><?php echo htmlentities($result->Testimonial);?></td>
                                                    <td><?php echo htmlentities($result->PostingDate);?></td>
                                                    <td><?php if($result->status=="" || $result->status==0)
{
	?><a href="testimonials.php?aeid=<?php echo htmlentities($result->id);?>" onclick="return confirm('Do you really want to Active')"> Inactive</a>
<?php } else {?>

<a href="testimonials.php?eid=<?php echo htmlentities($result->id);?>" onclick="return confirm('Do you really want to Inactive')"> Active</a>
</td>
<?php } ?></td>
                                                </tr>
                                                <?php $cnt=$cnt+1; } } ?>
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

