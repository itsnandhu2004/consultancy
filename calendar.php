<?php
include('includes/config.php');

try {
    // Fetch available events from the database
    $stmt = $dbh->query("SELECT id, event_name, base_price FROM events");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch booked dates with confirmed status
    $bookedDates = [];
    $stmt2 = $dbh->prepare("SELECT date_range FROM bookings WHERE status = 'confirmed'");
    $stmt2->execute();
    $results = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $dates = explode(',', $row['date_range']);
        foreach ($dates as $date) {
            $cleanDate = trim($date);
            if (!in_array($cleanDate, $bookedDates)) {
                $bookedDates[] = $cleanDate;
            }
        }
    }
} catch (PDOException $e) {
    die("Database Query Failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['event_id'], $_POST['date_range'], $_POST['total_price'], $_POST['user_email']) &&
        !empty($_POST['event_id']) && !empty($_POST['date_range']) && !empty($_POST['total_price']) && !empty($_POST['user_email'])
    ) {

        $eventId = $_POST['event_id'];
        $dateRange = $_POST['date_range'];
        $totalPrice = $_POST['total_price'];
        $userEmail = $_POST['user_email'];
        $slotDetails = json_encode($_POST['slot_details'] ?? []);

        // 🔍 Fetch the event name from the database based on event_id
        $stmt = $dbh->prepare("SELECT event_name FROM events WHERE id = ?");
        $stmt->execute([$eventId]);
        $eventData = $stmt->fetch(PDO::FETCH_ASSOC);
        $eventName = $eventData['event_name'] ?? '';

        try {
            // 💾 Insert event name into bookings
            $stmt = $dbh->prepare("INSERT INTO bookings (event_id, event_name, date_range, total_price, user_email, slot_details, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$eventId, $eventName, $dateRange, $totalPrice, $userEmail, $slotDetails]);

            header("Location: my-event-booking.php");
            exit();
        } catch (PDOException $e) {
            die("Database Insert Failed: " . $e->getMessage());
        }
    } else {
        die('Missing required fields.');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Multi-Day Event Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <style>
        body {
            padding: 20px;
        }

        .container {
            max-width: 700px;
        }

        .slot-options {
            margin-top: 10px;
        }

        #price_display {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }

        .booked-date {
            background-color: #dc3545 !important;
            color: white !important;
            text-decoration: line-through;
            cursor: not-allowed;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 class="mb-3">Book Your Event</h2>

        <form id="bookingForm" action="calendar.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Registered Email:</label>
                <input type="email" name="user_email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Select Event:</label>
                <select id="event" name="event_id" class="form-select" required>
                    <option value="">--Select an Event--</option>
                    <?php foreach ($events as $row) {
                        echo "<option value='{$row['id']}' data-price='{$row['base_price']}'>{$row['event_name']} - ₹{$row['base_price']}/day</option>";
                    } ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Choose Dates:</label>
                <input type="text" id="date_range" name="date_range" class="form-control" readonly required>
            </div>

            <div id="slotSelectionContainer"></div>

            <div id="price_display" class="alert alert-primary">Total Price: ₹0</div>

            <input type="hidden" id="total_price" name="total_price" value="0">
            <button type="submit" class="btn btn-success w-100">Book Now</button>
        </form>
    </div>

    <script>
        const bookedDates = <?php echo json_encode($bookedDates); ?>;

        $(document).ready(function () {
            $('#date_range').datepicker({
                format: "yyyy-mm-dd",
                multidate: true,
                todayHighlight: true,
                autoclose: false,
                beforeShowDay: function (date) {
                    const yyyyMMdd = date.toISOString().split('T')[0];
                    if (bookedDates.includes(yyyyMMdd)) {
                        return {
                            enabled: false,
                            classes: 'booked-date',
                            tooltip: 'Already Booked'
                        };
                    }
                    return true;
                }
            }).on("changeDate", function () {
                updateSlotSelection();
            });

            function updateSlotSelection() {
                let selectedDates = $('#date_range').val().split(",");
                let slotContainer = $('#slotSelectionContainer');
                slotContainer.empty();
                let basePrice = parseFloat($("#event option:selected").data("price")) || 0;

                selectedDates.forEach(date => {
                    if (date.trim() !== "") {
                        let slotHTML = `
                        <div class="mb-3">
                            <label class="form-label">Slot for ${date}:</label>
                            <select class="form-select slot-type" name="slot_details[${date}][slot_type]">
                                <option value="Full-Day" data-multiplier="1">Full-Day</option>
                                <option value="Half-Day" data-multiplier="0.5">Half-Day</option>
                                <option value="Custom" data-multiplier="custom">Custom Hours</option>
                            </select>
                            <div class="custom-hours d-none">
                                <label class="form-label">Start Time:</label>
                                <input type="time" class="form-control start-time" name="slot_details[${date}][start_time]">
                                <label class="form-label">End Time:</label>
                                <input type="time" class="form-control end-time" name="slot_details[${date}][end_time]">
                            </div>
                        </div>`;
                        slotContainer.append(slotHTML);
                    }
                });

                updatePrice();
            }

            $(document).on("change", ".slot-type", function () {
                let $customDiv = $(this).closest(".mb-3").find(".custom-hours");
                if ($(this).val() === "Custom") {
                    $customDiv.removeClass("d-none");
                } else {
                    $customDiv.addClass("d-none");
                }
                updatePrice();
            });

            $(document).on("change", ".start-time, .end-time", function () {
                updatePrice();
            });

            function updatePrice() {
                let basePrice = parseFloat($("#event option:selected").data("price")) || 0;
                let totalPrice = 0;

                $(".slot-type").each(function () {
                    let multiplier = $(this).find(":selected").data("multiplier");
                    let slotPrice = basePrice;

                    if (multiplier === "custom") {
                        let startTime = $(this).closest(".mb-3").find(".start-time").val();
                        let endTime = $(this).closest(".mb-3").find(".end-time").val();
                        if (startTime && endTime) {
                            let hours = (new Date("1970-01-01T" + endTime + "Z") - new Date("1970-01-01T" + startTime + "Z")) / (1000 * 60 * 60);
                            if (hours > 0) {
                                slotPrice = (basePrice / 24) * hours;
                            } else {
                                slotPrice = 0;
                            }
                        } else {
                            slotPrice = 0;
                        }
                    } else {
                        slotPrice *= parseFloat(multiplier);
                    }

                    totalPrice += slotPrice;
                });

                $("#price_display").text("Total Price: ₹" + totalPrice.toFixed(2));
                $("#total_price").val(totalPrice.toFixed(2));
            }
        });
    </script>

</body>

</html>




