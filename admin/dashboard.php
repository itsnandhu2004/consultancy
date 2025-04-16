<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
    exit();
}

// Fetch booked dates from DB
$bookedDates = [];
try {
    $stmt = $dbh->prepare("SELECT date_range FROM bookings WHERE status = 'confirmed'");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $dates = explode(',', $row['date_range']);
        foreach ($dates as $date) {
            $bookedDates[] = trim($date);
        }
    }
} catch (PDOException $e) {
    // Handle error
}
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Snappy Boys Portal | Admin Dashboard</title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap-social.css">
    <link rel="stylesheet" href="css/bootstrap-select.css">
    <link rel="stylesheet" href="css/fileinput.min.css">
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
    <link rel="stylesheet" href="css/style.css">

    <!-- FullCalendar -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <style>
        #calendar {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
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
                    <h2 class="page-title">Dashboard</h2>
                </div>
            </div>

            <!-- Calendar Section -->
            <div class="row">
                <div class="col-md-12">
                    <h4 class="page-title text-center">Booking Calendar</h4>
                    <div id="calendar"></div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Scripts -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-select.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<script src="js/fileinput.js"></script>
<script src="js/Chart.min.js"></script>
<script src="js/chartData.js"></script>
<script src="js/main.js"></script>

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    const bookedDates = <?php echo json_encode($bookedDates); ?>;

    const events = bookedDates.map(date => ({
        title: 'Booked',
        start: date,
        allDay: true,
        backgroundColor: '#ff4d4d',
        borderColor: '#cc0000'
    }));

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        events: events
    });

    calendar.render();
});
</script>
</body>
</html>
