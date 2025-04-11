<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) { 
    header('location:index.php');
} else {
    if(isset($_POST['submit'])) {
        $brand = $_POST['brand'];
        $id = $_GET['id'];
        $sql = "UPDATE tblbrands SET BrandName=:brand WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':brand', $brand, PDO::PARAM_STR);
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $query->execute();
        $msg = "Brand Updated Successfully";
    }
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Car Rental Portal | Admin Update Brand</title>
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background-color: #ecf0f1; font-family: 'Arial', sans-serif; }
        .container-fluid { max-width: 500px; margin: auto; }
        .panel { border-radius: 10px; border: none; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2); }
        .panel-heading { background-color: #2980b9 !important; color: white !important; font-weight: bold; border-radius: 10px 10px 0 0; text-align: center; }
        .panel-body { min-height: 300px; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .form-container { width: 450px; padding: 30px; background: white; border-radius: 10px; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2); }
        .btn-primary { background-color: #2980b9; border: none; width: 100%; }
        .btn-primary:hover { background-color: #1c6ea4; }
        .form-control { border-radius: 5px; }
        .errorWrap { padding: 10px; margin-bottom: 20px; background: #fff; border-left: 4px solid #e74c3c; box-shadow: 0 1px 1px rgba(0,0,0,.1); }
        .succWrap { padding: 10px; margin-bottom: 20px; background: #fff; border-left: 4px solid #2ecc71; box-shadow: 0 1px 1px rgba(0,0,0,.1); }
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
                        <h2 class="page-title text-center">Update Brand</h2>
                        <div class="row justify-content-center">
                            <div class="col-md-12 d-flex justify-content-center">
                                <div class="panel panel-default form-container">
                                    <div class="panel-heading">Update Brand</div>
                                    <div class="panel-body">
                                        <form method="post" class="form-horizontal">
                                            <?php if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php } ?>
                                            <?php 
                                            $id = $_GET['id'];
                                            $ret = "SELECT * FROM tblbrands WHERE id=:id";
                                            $query = $dbh->prepare($ret);
                                            $query->bindParam(':id', $id, PDO::PARAM_STR);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            if($query->rowCount() > 0) {
                                                foreach($results as $result) { ?>
                                                    <div class="form-group">
                                                        <label class="control-label">Brand Name</label>
                                                        <input type="text" class="form-control" value="<?php echo htmlentities($result->BrandName); ?>" name="brand" required>
                                                    </div>
                                            <?php }} ?>
                                            <div class="form-group">
                                                <button class="btn btn-primary" name="submit" type="submit">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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