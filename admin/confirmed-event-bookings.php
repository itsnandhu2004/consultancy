

<?php
session_start();
error_reporting(E_ALL);

// Include database configuration
include('includes/config.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Set PDO error mode to exception
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch confirmed bookings
    $sql = "SELECT * FROM bookings WHERE status = 'confirmed'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    // Debugging: Check if records are fetched
    if (empty($results)) {
        echo "<p style='color: red; text-align: center;'>No confirmed bookings found.</p>";
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Snappy Boys Portal | Confirmed Bookings</title>
    
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table-container {
            max-width: 90%;
            margin: auto;
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
        }
        .table thead {
            background-color: #2980b9 !important;
            color: white !important;
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
                        <h2 class="page-title text-center">Confirmed Event Bookings</h2>
                        <div class="panel panel-default table-container">
                            <div class="panel-heading">Bookings Info</div>
                            <div class="panel-body">
                                <table id="bookingTable" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Event Name</th>
                                            <th>User Email</th>
                                            <th>Date Range</th>
                                            <th>Slot Details</th>
                                            <th>Total Price</th>
                                            <th>Booking Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $row) { ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                                                <td><?php echo htmlspecialchars($row['date_range']); ?></td>
                                                <td>
                            <?php 
                            $slot_details = json_decode($row['slot_details'], true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                echo "<span class='text-danger'>Invalid JSON Data</span>";
                            } elseif (is_array($slot_details) && !empty($slot_details)) {
                                foreach ($slot_details as $slot) {
                                    if (is_array($slot)) {
                                        $date = isset($slot['date']) ? htmlspecialchars($slot['date']) : 'N/A';
                                        $slot_type = isset($slot['slot_type']) ? htmlspecialchars($slot['slot_type']) : 'N/A';
                                        echo "<strong>" . $date . ":</strong> " . $slot_type . "<br>";
                                    } else {
                                        echo "<strong>N/A:</strong> N/A<br>";
                                    }
                                }
                            } else {
                                echo "No slot details available";
                            }
                            ?>
                        </td>
                                                <td><?php echo htmlspecialchars($row['total_price']); ?></td>
                                                <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                                                <td><span class="badge bg-success">Confirmed</span></td>
                                            </tr>
                                        <?php } ?>
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
    <script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
  
    <script>
        $(document).ready(function() {
            $('#bookingTable').DataTable({
                "paging": false,          // Disables pagination
                "info": false,            // Hides the "Showing X to Y of Z entries"
                "searching": false,       // Disables the search box
                "ordering": false         // Disables column sorting
            });
        });
    </script>
</body>
</html>
