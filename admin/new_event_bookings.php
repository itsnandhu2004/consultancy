
<?php
session_start();
error_reporting(0);

include('includes/config.php');

// Handle Confirm and Cancel Actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['booking_id'];
    
    if (isset($_POST['confirm'])) {
        $status = "confirmed";
    } elseif (isset($_POST['cancel'])) {
        $status = "cancelled";
    }

    $sql = "UPDATE bookings SET status = :status WHERE id = :booking_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
    $query->execute();
}

// Fetch New Bookings
$sql = "SELECT * FROM bookings WHERE status = 'pending'";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);
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
                        <h2 class="page-title text-center">New Event Bookings</h2>
                        <div class="panel panel-default table-container">
                            <div class="panel-heading">Bookings Info</div>
                            <div class="panel-body">
                                <table id="bookingTable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Event Name</th>
                                            <th>User Email</th>
                                            <th>Date Range</th>
                                            <th>Slot Details</th>
                                            <th>Total Price</th>
                                            <th>Booking Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql = "SELECT * FROM bookings WHERE status = 'pending'";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_ASSOC);
                                        $cnt = 1;
                                        foreach ($results as $row) { ?>
                                            <tr>
                                                <td><?php echo htmlentities($cnt);?></td>
                                                <td><?php echo htmlentities($row['event_name']);?></td>
                                                <td><?php echo htmlentities($row['user_email']);?></td>
                                                <td><?php echo htmlentities($row['date_range']);?></td>
                                                <td><?php 
                                                    $slot_details = json_decode($row['slot_details'], true);
                                                    if (is_array($slot_details) && !empty($slot_details)) {
                                                        foreach ($slot_details as $slot) {
                                                            if (is_array($slot)) {
                                                                echo "<strong>" . htmlentities($slot['date']) . ":</strong> " . htmlentities($slot['slot_type']) . "<br>";
                                                            } else {
                                                                echo "<strong>N/A:</strong> N/A<br>";
                                                            }
                                                        }
                                                    } else {
                                                        echo "No slot details available";
                                                    }
                                                ?></td>
                                                <td><?php echo htmlentities($row['total_price']);?></td>
                                                <td><?php echo htmlentities($row['booking_date']);?></td>
                                                <td>
                                                    <form method="POST">
                                                        <input type="hidden" name="booking_id" value="<?php echo htmlentities($row['id']); ?>">
                                                        <button type="submit" name="confirm" class="btn btn-success">Confirm</button>
                                                        <button type="submit" name="cancel" class="btn btn-danger">Cancel</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php $cnt++; } ?>
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

