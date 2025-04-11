<?php
session_start();
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    $msg = "";
    $error = "";

    // DELETE SERVICE
    if (isset($_GET['del'])) {
        $id = intval($_GET['del']);
        $sql = "DELETE FROM services WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $msg = "Service deleted successfully.";
    }

    // ADD OR UPDATE SERVICE
    if (isset($_POST['submit'])) {
        $serviceName = $_POST['service_name'];
        $serviceDesc = $_POST['service_description'];
        $image = $_FILES['image']['name'];
        $target_dir = "img/services/";
        $target_file = $target_dir . basename($_FILES['image']['name']);
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (!empty($image) && !in_array($ext, $allowed)) {
            $error = "Invalid file format. Please upload JPG, JPEG, or PNG.";
        } else {
            if (!empty($image)) {
                move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
            }

            if (isset($_POST['id']) && !empty($_POST['id'])) {
                // UPDATE SERVICE
                $id = $_POST['id'];
                if (!empty($image)) {
                    $sql = "UPDATE services SET service_name=:service_name, service_description=:service_description, image=:image WHERE id=:id";
                } else {
                    $sql = "UPDATE services SET service_name=:service_name, service_description=:service_description WHERE id=:id";
                }
                $query = $dbh->prepare($sql);
                $query->bindParam(':id', $id, PDO::PARAM_INT);
            } else {
                // INSERT NEW SERVICE
                $sql = "INSERT INTO services (service_name, service_description, image) VALUES (:service_name, :service_description, :image)";
                $query = $dbh->prepare($sql);
            }

            $query->bindParam(':service_name', $serviceName, PDO::PARAM_STR);
            $query->bindParam(':service_description', $serviceDesc, PDO::PARAM_STR);
            if (!empty($image)) {
                $query->bindParam(':image', $image, PDO::PARAM_STR);
            }
            $query->execute();

            $msg = isset($_POST['id']) ? "Service updated successfully." : "Service added successfully.";
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Manage Services</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .panel-heading {
            background-color: #2980b9 !important;
            color: white !important;
            font-size: 18px;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
        }
        .panel {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-control {
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #2980b9;
            border: none;
        }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>
<div class="ts-main-content">
    <?php include('includes/leftbar.php'); ?>
    <div class="content-wrapper">
        <div class="container-fluid">

            <h2 class="page-title text-center">Manage Services</h2>

            <?php if (!empty($msg)) { ?>
                <div class="alert alert-success"><?php echo htmlentities($msg); ?></div>
            <?php } elseif (!empty($error)) { ?>
                <div class="alert alert-danger"><?php echo htmlentities($error); ?></div>
            <?php } ?>

            <!-- Service Form -->
            <div class="panel panel-default">
                <div class="panel-heading">Add / Edit Service</div>
                <div class="panel-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="serviceId">
                        <div class="form-group">
                            <label>Service Name</label>
                            <input type="text" class="form-control" name="service_name" id="service_name" required>
                        </div>
                        <div class="form-group">
                            <label>Service Description</label>
                            <textarea class="form-control" name="service_description" id="service_description" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Upload Image</label>
                            <input type="file" class="form-control" name="image" id="image">
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Save Service</button>
                    </form>
                </div>
            </div>

            <!-- Service List -->
            <div class="panel panel-default mt-4">
                <div class="panel-heading">Service List</div>
                <div class="panel-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Service Name</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql = "SELECT * FROM services";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                        $cnt = 1;

                        if ($query->rowCount() > 0) {
                            foreach ($results as $result) { ?>
                                <tr>
                                    <td><?php echo htmlentities($cnt); ?></td>
                                    <td><?php echo htmlentities($result->service_name); ?></td>
                                    <td><?php echo htmlentities($result->service_description); ?></td>
                                    <td>
                                        <?php if (!empty($result->image)) { ?>
                                             <img src="img/vehicleimages/<?php echo htmlentities($result->image); ?>" width="80">
                                        <?php } else { ?>
                                            No Image
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-sm edit-btn"
                                                data-id="<?php echo htmlentities($result->id); ?>"
                                                data-name="<?php echo htmlentities($result->service_name); ?>"
                                                data-desc="<?php echo htmlentities($result->service_description); ?>">
                                            Edit
                                        </button>
                                        <a href="add-service.php?del=<?php echo htmlentities($result->id); ?>"
                                           onclick="return confirm('Are you sure you want to delete this service?');"
                                           class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                            <?php $cnt++; }
                        } else { ?>
                            <tr>
                                <td colspan="5">No services found.</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const serviceId = this.getAttribute('data-id');
            const serviceName = this.getAttribute('data-name');
            const serviceDesc = this.getAttribute('data-desc');

            document.getElementById('serviceId').value = serviceId;
            document.getElementById('service_name').value = serviceName;
            document.getElementById('service_description').value = serviceDesc;

            window.scrollTo({
                top: document.querySelector("form").offsetTop,
                behavior: "smooth"
            });
        });
    });
</script>
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