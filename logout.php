<?php
include 'db.php';

// Log the logout action
if (isset($_SESSION['staff_id'])) {
    $staff_id = $_SESSION['staff_id'];
    $log_action = $conn->prepare("INSERT INTO logs (action, staff_id) VALUES ('Logout', ?)");
    $log_action->bind_param("s", $staff_id);
    $log_action->execute();
}

session_unset();
session_destroy();
header("Location: index.php");
exit;
?>