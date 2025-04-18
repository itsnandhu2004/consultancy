<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    die('Unauthorized access.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $action = $_POST['action'] ?? '';

    if ($date && in_array($action, ['block', 'unblock'])) {
        try {
            if ($action === 'block') {
                $stmt = $dbh->prepare("INSERT IGNORE INTO blocked_dates (blocked_date) VALUES (?)");
                $stmt->execute([$date]);
                echo "Date blocked successfully.";
            } elseif ($action === 'unblock') {
                $stmt = $dbh->prepare("DELETE FROM blocked_dates WHERE blocked_date = ?");
                $stmt->execute([$date]);
                echo "Date unblocked successfully.";
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    } else {
        echo "Invalid input.";
    }
}
?>
