
<?php
session_start();
include('includes/config.php');

// Save form data after POST and redirect to same page (PRG)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['filters'] = $_POST;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$filters = $_SESSION['filters'] ?? null;
unset($_SESSION['filters']); // Only show once after redirect

// Fetch dropdown options
$users = $dbh->query("SELECT DISTINCT user_email FROM bookings")->fetchAll(PDO::FETCH_COLUMN);
$events = $dbh->query("SELECT DISTINCT event_name FROM bookings")->fetchAll(PDO::FETCH_COLUMN);

// Initialize filters
$status = $filters['status'] ?? 'all';
$from_date = $filters['from_date'] ?? '';
$to_date = $filters['to_date'] ?? '';
$email = $filters['email'] ?? '';
$event = $filters['event'] ?? '';
$slot_date = $filters['slot_date'] ?? '';

$results = [];

if ($filters) {
    $sql = "SELECT * FROM bookings WHERE 1=1";
    $params = [];

    if ($status !== 'all') {
        $sql .= " AND status = :status";
        $params[':status'] = $status;
    }
    if (!empty($from_date) && !empty($to_date)) {
        $sql .= " AND DATE(booking_date) BETWEEN :from_date AND :to_date";
        $params[':from_date'] = $from_date;
        $params[':to_date'] = $to_date;
    }
    if (!empty($email)) {
        $sql .= " AND user_email = :email";
        $params[':email'] = $email;
    }
    if (!empty($event)) {
        $sql .= " AND event_name = :event";
        $params[':event'] = $event;
    }
    if (!empty($slot_date)) {
        $sql .= " AND slot_details LIKE :slot_date";
        $params[':slot_date'] = '%' . $slot_date . '%';
    }

    $query = $dbh->prepare($sql);
    $query->execute($params);
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        Event Booking Report
    </title>
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
    <style>
        body { background-color: #f8f9fa; font-family: 'Arial', sans-serif; }
        .table-container { max-width: 90%; margin: 20px auto; }
        .panel { border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .panel-heading { background-color: #2980b9 !important; color: white !important; font-size: 20px; font-weight: bold; text-transform: uppercase; border-radius: 10px 10px 0 0; }
        .table thead { background-color: #2980b9 !important; color: white !important; }
        .table tbody tr:hover { background-color: #d6e9f9 !important; font-weight: bold; }
        .btn-action { font-weight: bold; font-size: 14px; }
        .select2-container { min-width: 200px; }
        .filter-group label { margin-right: 10px; font-weight: bold; }
        .badge-success { background-color: #28a745; }
        .badge-danger { background-color: #dc3545; }
        h2.page-title { font-weight: bold; margin: 20px 0; }
        .report-container { padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
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
                        <h2 class="page-title text-center">Event Bookings Report</h2>
                        <div class="panel panel-default table-container">
                            <div class="panel-heading"></div>
                            <div class="panel-body">
                            <div class="report-container">

<!-- Filter Form -->
<form method="POST" class="row mb-4 g-3">
    <div class="col-md-2">
        <label>Status:</label>
        <select name="status" class="form-control">
            <option value="all" <?= ($status === 'all') ? 'selected' : '' ?>>All</option>
            <option value="confirmed" <?= ($status === 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
            <option value="cancelled" <?= ($status === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
        </select>
    </div>

    <div class="col-md-2">
        <label>From:</label>
        <input type="date" name="from_date" class="form-control" value="<?= $from_date ?>">
    </div>

    <div class="col-md-2">
        <label>To:</label>
        <input type="date" name="to_date" class="form-control" value="<?= $to_date ?>">
    </div>

    <div class="col-md-3">
        <label>User Email:</label>
        <select name="email" class="form-control select2">
            <option value="">All</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user ?>" <?= $user === $email ? 'selected' : '' ?>><?= $user ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-2">
        <label>Event Name:</label>
        <select name="event" class="form-control">
            <option value="">All</option>
            <?php foreach ($events as $e): ?>
                <option value="<?= $e ?>" <?= $e === $event ? 'selected' : '' ?>><?= $e ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-2">
        <label>Slot Date:</label>
        <input type="date" name="slot_date" class="form-control" value="<?= $slot_date ?>">
    </div>

    <div class="col-md-2 align-self-end">
        <button type="submit" class="btn btn-primary">Generate</button>
    </div>
</form>

<?php if (!empty($filters)): ?>
<!-- DataTable -->
<table id="reportTable" class="table table-bordered table-striped">
<thead>
        <tr>
            <th>#</th>
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
        <?php $cnt = 1; foreach ($results as $row): ?>
            <tr>
                <td><?= $cnt++ ?></td>
                <td><?= htmlentities($row['event_name']) ?></td>
                <td><?= htmlentities($row['user_email']) ?></td>
                <td><?= htmlentities($row['date_range']) ?></td>
                <td>
                    <?php 
                    $slot_details = json_decode($row['slot_details'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        echo "<span class='text-danger'>Invalid JSON Data</span>";
                    } elseif (is_array($slot_details) && !empty($slot_details)) {
                        foreach ($slot_details as $slot) {
                            if (is_array($slot)) {
                                $date = $slot['date'] ?? 'N/A';
                                $slot_type = $slot['slot_type'] ?? 'N/A';
                                echo "<strong>$date:</strong> $slot_type<br>";
                            } else {
                                echo "<strong>N/A:</strong> N/A<br>";
                            }
                        }
                    } else {
                        echo "No slot details available";
                    }
                    ?>
                </td>
                <td><?= htmlentities($row['total_price']) ?></td>
                <td><?= htmlentities($row['booking_date']) ?></td>
                <td><span class="badge badge-<?= $row['status'] == 'confirmed' ? 'success' : 'danger' ?>"><?= ucfirst($row['status']) ?></span></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
</table>
<?php endif; ?>


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
    
<!-- JS Includes -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>

<!-- DataTables Export Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        $('.select2').select2({ placeholder: "Select Email" });

        <?php if (!empty($filters)): ?>
$('#reportTable').DataTable({
    dom: 'Bfrtip',
    buttons: ['excel', 'csv', 'pdf', 'print'],
    order: [[0, 'asc']]
});
<?php endif; ?>
    });
</script>
</body>
</html>
<?php  ?>


