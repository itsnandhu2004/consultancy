<?php
session_start();
error_reporting(0);
include('includes/config.php');

$error = "";
$msg = "";

// Handle add/update event
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    $base_price = $_POST['base_price'];
    $id = $_POST['id'] ?? null;

    if (empty($event_name) || empty($base_price)) {
        $error = "Event Name and Base Price are required!";
    } else {
        try {
            if ($id) {
                $stmt = $dbh->prepare("UPDATE events SET event_name = :event_name, base_price = :base_price WHERE id = :id");
                $stmt->execute(['event_name' => $event_name, 'base_price' => $base_price, 'id' => $id]);
                $msg = "Event updated successfully.";
            } else {
                $stmt = $dbh->prepare("INSERT INTO events (event_name, base_price) VALUES (:event_name, :base_price)");
                $stmt->execute(['event_name' => $event_name, 'base_price' => $base_price]);
                $msg = "Event added successfully.";
            }
            header("Location: manage-events.php");
            exit();
        } catch (PDOException $e) {
            $error = "SQL Error: " . $e->getMessage();
        }
    }
}

// Handle delete
if (isset($_GET['delete_id'])) {
    try {
        $stmt = $dbh->prepare("DELETE FROM events WHERE id = :id");
        $stmt->execute(['id' => $_GET['delete_id']]);
        header("Location: manage-events.php");
        exit();
    } catch (PDOException $e) {
        $error = "SQL Error: " . $e->getMessage();
    }
}

// Fetch all events
$stmt = $dbh->prepare("SELECT * FROM events");
$stmt->execute();
$events = $stmt->fetchAll();

// Check if editing
$event_to_edit = null;
if (isset($_GET['edit_id'])) {
    $stmt = $dbh->prepare("SELECT * FROM events WHERE id = :id");
    $stmt->execute(['id' => $_GET['edit_id']]);
    $event_to_edit = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Manage Events</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS Links -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        body { background-color: #f4f7f9; font-family: Arial, sans-serif; }
        .panel { border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-top: 30px; }
        .panel-heading { background-color: #3498db !important; color: white !important; font-weight: bold; font-size: 18px; border-radius: 10px 10px 0 0; }
        .page-title { font-weight: bold; text-align: center; margin: 20px 0; color: #2c3e50; }
        .btn { font-weight: bold; }
        .table-container { max-width: 95%; margin: 30px auto; }
        .table thead { background-color: #3498db; color: #fff; }
        .table-hover tbody tr:hover { background-color: #d6e9f9; font-weight: bold; }
        .form-control { border-radius: 5px; }
        .form-group label { font-weight: bold; }
        .succWrap, .errorWrap {
            padding: 10px; margin-bottom: 20px;
            background: #fff; border-left: 4px solid;
            box-shadow: 0 1px 1px rgba(0, 0, 0, .1);
        }
        .succWrap { border-color: #5cb85c; }
        .errorWrap { border-color: #d9534f; }
    </style>
</head>
<body>
<?php include('includes/header.php'); ?>
<div class="ts-main-content">
    <?php include('includes/leftbar.php'); ?>

    <div class="content-wrapper">
        <div class="container-fluid">

            <h2 class="page-title"><?php echo $event_to_edit ? 'Edit' : 'Add'; ?> Event</h2>

            <!-- Add/Edit Event Form -->
            <div class="panel panel-default table-container">
                <div class="panel-heading"><?php echo $event_to_edit ? 'Edit Event' : 'Add Event'; ?></div>
                <div class="panel-body">
                    <?php if ($error): ?>
                        <div class="errorWrap"><strong>ERROR:</strong> <?php echo htmlentities($error); ?></div>
                    <?php elseif ($msg): ?>
                        <div class="succWrap"><strong>SUCCESS:</strong> <?php echo htmlentities($msg); ?></div>
                    <?php endif; ?>
                    <form action="manage-events.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $event_to_edit['id'] ?? ''; ?>">
                        <div class="form-group">
                            <label for="event_name">Event Name:</label>
                            <input type="text" class="form-control" id="event_name" name="event_name" value="<?php echo $event_to_edit['event_name'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="base_price">Base Price (per day):</label>
                            <input type="number" step="0.01" class="form-control" id="base_price" name="base_price" value="<?php echo $event_to_edit['base_price'] ?? ''; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo $event_to_edit ? 'Update' : 'Add'; ?> Event</button>
                    </form>
                </div>
            </div>

            <!-- Event List Table -->
            <h2 class="page-title">All Events</h2>
            <div class="panel panel-default table-container">
                <div class="panel-heading">Events List</div>
                <div class="panel-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Event Name</th>
                                <th>Base Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($events): foreach ($events as $event): ?>
                            <tr>
                                <td><?php echo $event['id']; ?></td>
                                <td><?php echo $event['event_name']; ?></td>
                                <td><?php echo $event['base_price']; ?></td>
                                <td>
                                    <a href="manage-events.php?edit_id=<?php echo $event['id']; ?>" class="btn btn-success btn-sm">Edit</a>
                                    <a href="manage-events.php?delete_id=<?php echo $event['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="4" class="text-center">No events found.</td></tr>
                        <?php endif; ?>
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
