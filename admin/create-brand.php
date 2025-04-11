<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{ 
header('location:index.php');
}
else{
// Code for change password	
if(isset($_POST['submit']))
{
$brand=$_POST['brand'];
$sql="INSERT INTO  tblbrands(BrandName) VALUES(:brand)";
$query = $dbh->prepare($sql);
$query->bindParam(':brand',$brand,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
$msg="Brand Created successfully";
}
else 
{
$error="Something went wrong. Please try again";
}

}
?>

<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="theme-color" content="#2c3e50">
	
	<title>Car Rental Portal | Admin Create Brand</title>

	<!-- Font awesome -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Custom Styles -->
	<link rel="stylesheet" href="css/style.css">
  <style>
  body {
      font-family: 'Arial', sans-serif;
      background-color: #ecf0f1;
  }
  .container-fluid {
    max-width: 500px; /* Increased width */
    margin: auto;
}
  .panel {
      border-radius: 10px;
      border: none;
      box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
  }
  .panel-heading {
      background-color: #2980b9 !important;
      color: white !important;
      font-weight: bold;
      border-radius: 10px 10px 0 0;
      text-align: center;
  }
  .panel-body {
    min-height: 300px; /* Increased height */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
  .form-container {
    width: 450px; /* Increased width */
    padding: 30px; /* Increased padding */
    background: white;
    border-radius: 10px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
}

  .btn-primary {
      background-color: #2980b9;
      border: none;
      width: 100%;
  }
  .btn-primary:hover {
      background-color: #1c6ea4;
  }
  .form-control {
      border-radius: 5px;
  }
  .errorWrap {
      padding: 10px;
      margin: 0 0 20px 0;
      background: #fff;
      border-left: 4px solid #e74c3c;
      box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
  }
  .succWrap{
      padding: 10px;
      margin: 0 0 20px 0;
      background: #fff;
      border-left: 4px solid #2ecc71;
      box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
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
						<h2 class="page-title text-center">Create Brand</h2>
						<div class="row justify-content-center">
							<div class="col-md-12 d-flex justify-content-center">
								<div class="panel panel-default form-container">
									<div class="panel-heading">Create Brand</div>
									<div class="panel-body">
										<form method="post" name="chngpwd" class="form-horizontal">
											<?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
											else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
											<div class="form-group">
												<label class="control-label">Brand Name</label>
												<input type="text" class="form-control" name="brand" id="brand" required>
											</div>
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

	<!-- Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/main.js"></script>
</body>
</html>
<?php } ?>