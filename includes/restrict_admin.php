<?php
// This must be included AFTER db.php (which starts the session)

if (!isset($_SESSION['is_super_admin']) || $_SESSION['is_super_admin'] != 1) {
    // Log attempted unauthorized access
    $log_staff_id = $_SESSION['staff_id'] ?? 'N/A';
    $log_action = $conn->prepare("INSERT INTO logs (action, staff_id) VALUES (?, ?)");
    $log_action->bind_param("ss", "Attempted Unauthorized Admin Access", $log_staff_id);
    $log_action->execute();
    
    // Redirect to dashboard or login
    header("Location: ../dashboard.php");
    exit;
}
?>