<?php
session_start();
include('includes/config.php');

// Fetch events
$stmt = $dbh->prepare("SELECT * FROM events");
$stmt->execute();
$events = $stmt->fetchAll();

// Fetch blocked dates
$stmt = $dbh->prepare("SELECT blocked_date FROM blocked_dates");
$stmt->execute();
$blocked_dates = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

// Fetch booked dates
$booked_dates = [];
$stmt = $dbh->prepare("SELECT event_dates FROM event_bookings");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($bookings as $booking) {
    $booked_dates = array_merge($booked_dates, explode(',', $booking['event_dates']));
}
$blocked_dates = array_merge($blocked_dates, $booked_dates);

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_email = $_POST['user_email'] ?? '';
    $event_id = $_POST['event_id'] ?? '';
    $event_dates = $_POST['event_dates'] ?? [];

    if (empty($event_id) || empty($event_dates)) {
        echo "<div class='alert alert-danger'>Please select a valid event and at least one date.</div>";
    } else {
        $event_dates_str = implode(',', $event_dates);
        $booking_id = uniqid("BOOK_");
        $razorpay_order_id = uniqid("ORDER_");
        $razorpay_payment_id = '';
        $booked_date = date("Y-m-d");

        // Fetch event details
        $stmt = $dbh->prepare("SELECT event_name, base_price FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($event) {
            $total_price = floatval($_POST['calculated_total_price']);

            // Check for duplicate booking
            $checkStmt = $dbh->prepare("SELECT COUNT(*) FROM event_bookings WHERE event_id = ? AND event_dates = ? AND user_email = ?");
            $checkStmt->execute([$event_id, $event_dates_str, $user_email]);
            $existingBooking = $checkStmt->fetchColumn();

            if ($existingBooking == 0) {
                $stmt = $dbh->prepare("INSERT INTO event_bookings 
                    (event_id, event_name, event_dates, total_price, status, booked_date, razorpay_order_id, razorpay_payment_id, booking_id, user_email) 
                    VALUES (?, ?, ?, ?, 'Pending Approval', ?, ?, ?, ?, ?)");

                if ($stmt->execute([ 
                    $event_id,
                    $event['event_name'],
                    $event_dates_str,
                    $total_price,
                    $booked_date,
                    $razorpay_order_id,
                    $razorpay_payment_id,
                    $booking_id,
                    $user_email
                ])) {
                    echo "<div class='alert alert-success'>Booking successful! Please complete payment.</div>";
                    // Redirect to avoid resubmission
                    header("Location: " . $_SERVER['PHP_SELF']); // or reload the same page: header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    $errorInfo = $stmt->errorInfo();
                    echo "<div class='alert alert-danger'>Error: " . $errorInfo[2] . "</div>";
                }
            } else {
                echo "<div class='alert alert-warning'>⚠️ You have already booked this event for the selected dates.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Invalid event selected.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Booking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #fdfbfb, #ebedee);
            font-family: 'Poppins', sans-serif;
            color: #333;
            margin: 0;
            padding: 40px 0;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #222;
            text-align: center;
            margin-bottom: 30px;
        }

        h3 {
            margin-top: 30px;
            font-size: 1.4rem;
            color: #555;
            border-left: 4px solid #007bff;
            padding-left: 10px;
        }

        .list-group-item {
            background-color: #f0f4ff;
            border: none;
            padding: 12px 20px;
            margin-bottom: 10px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .list-group-item:hover {
            background-color: #d6e4ff;
        }

        .form-group label {
            font-weight: 600;
            margin-top: 15px;
            display: block;
            color: #333;
        }

        .form-control {
            width: 100%;
            max-width: 100%;
            padding: 14px 16px;
            font-size: 15px;
            border-radius: 8px;
            border: 1px solid #ced4da;
            box-sizing: border-box;
            overflow-x: auto;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0,123,255,0.3);
            outline: none;
        }

        .btn-primary {
            display: block;
            width: 100%;
            background: linear-gradient(90deg, #007bff, #00c6ff);
            border: none;
            color: white;
            font-size: 16px;
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
            transition: background 0.3s ease;
            margin-top: 20px;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #0056b3, #0099cc);
        }

        #event_dates {
            background: url('https://cdn-icons-png.flaticon.com/512/747/747310.png') no-repeat right 1rem center;
            background-size: 20px;
            cursor: pointer;
        }

        .flatpickr-day.flatpickr-disabled {
            background-color: red !important;
            color: white !important;
            pointer-events: none !important;
        }

        .flatpickr-day.flatpickr-disabled:hover {
            background-color: red !important;
            color: white !important;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Event Booking</h1>

    <h3>Available Events</h3>
    <ul class="list-group">
        <?php foreach ($events as $event): ?>
            <li class="list-group-item">
                <strong><?= htmlspecialchars($event['event_name']); ?></strong> - ₹<?= number_format($event['base_price'], 2); ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <h3>Book Your Event</h3>
    <form method="POST" action="">
        <div class="form-group">
            <label for="user_email">Your Email:</label>
            <input type="email" class="form-control" name="user_email" id="user_email" required>
        </div>

        <div class="form-group">
            <label for="event">Select Event:</label>
            <select class="form-control" id="event" name="event_id" required>
                <option value="">Choose an event</option>
                <?php foreach ($events as $event): ?>
                    <option value="<?= $event['id']; ?>">
                        <?= htmlspecialchars($event['event_name']); ?> - ₹<?= number_format($event['base_price'], 2); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="event_dates">Choose Dates:</label>
            <input type="text" class="form-control" id="event_dates" name="event_dates[]" required placeholder="Pick dates">
        </div>

        <div class="form-group">
            <label>Total Price:</label>
            <p id="total_price_display"><strong>₹0.00</strong></p>
            <input type="hidden" name="calculated_total_price" id="calculated_total_price">
        </div>

        <button type="submit" class="btn btn-primary" id="bookNowBtn">Book Now</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    const blockedDates = <?php echo json_encode($blocked_dates); ?>;
    const eventPrices = {};
    <?php foreach ($events as $event): ?>
        eventPrices[<?= $event['id']; ?>] = <?= $event['base_price']; ?>;
    <?php endforeach; ?>

    let selectedDates = [];

    function calculateTotal() {
        const eventId = document.getElementById("event").value;
        const price = eventPrices[eventId] || 0;
        const total = selectedDates.length * price;
        document.getElementById("total_price_display").innerHTML = "<strong>₹" + total.toFixed(2) + "</strong>";
        document.getElementById("calculated_total_price").value = total.toFixed(2);
    }

    flatpickr("#event_dates", {
        mode: "multiple",
        dateFormat: "Y-m-d",
        minDate: "today",
        disable: blockedDates.map(d => new Date(d)),
        onChange: function(dates) {
            selectedDates = dates;
            calculateTotal();
        }
    });

    document.getElementById("event").addEventListener("change", calculateTotal);
</script>
<script src="js/bootstrap.min.js"></script>
<script>
    document.querySelector("form").addEventListener("submit", function() {
        const btn = document.getElementById("bookNowBtn");
        btn.disabled = true;
        btn.innerText = "Processing...";
    });
</script>

</body>
</html>
