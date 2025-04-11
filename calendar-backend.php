<?php
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['event_id'], $_POST['date_range'], $_POST['total_price'], $_POST['user_email']) &&
        !empty($_POST['event_id']) && !empty($_POST['date_range']) && !empty($_POST['total_price']) && !empty($_POST['user_email'])) {

        $eventId = $_POST['event_id'];
        $dateRange = $_POST['date_range'];
        $totalPrice = $_POST['total_price'];
        $userEmail = $_POST['user_email'];
        $slotDetails = json_encode($_POST['slot_details'] ?? []);
        $eventName = "";

        // Fetch event name
        try {
            $stmt = $dbh->prepare("SELECT event_name FROM events WHERE id = ?");
            $stmt->execute([$eventId]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($event) {
                $eventName = $event['event_name'];
            } else {
                die("Invalid event selected.");
            }
        } catch (PDOException $e) {
            die("Database Query Failed: " . $e->getMessage());
        }

        // Insert booking data into the database
        try {
            $stmt = $dbh->prepare("INSERT INTO bookings (event_id, event_name, user_email, date_range, slot_details, total_price, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$eventId, $eventName, $userEmail, $dateRange, $slotDetails, $totalPrice]);
            echo "Booking successful!";
        } catch (PDOException $e) {
            die("Database Insert Failed: " . $e->getMessage());
        }
    } else {
        die('Missing required fields.');
    }
} else {
    die('Invalid request method.');
}
?>
