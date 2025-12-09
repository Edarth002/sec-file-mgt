<?php
include 'db.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request.");
}

$file_id = (int)$_GET['id'];
$staff_id = $_SESSION['staff_id'];

$download_stmt = $conn->prepare("SELECT file_name, stored_path FROM documents WHERE id = ?");
$download_stmt->bind_param("i", $file_id);
$download_stmt->execute();
$file_data = $download_stmt->get_result()->fetch_assoc();

if ($file_data && file_exists($file_data['stored_path'])) {
    // 1. Log the download action
    $log_action = $conn->prepare("INSERT INTO logs (action, file_name, staff_id) VALUES ('File Downloaded', ?, ?)");
    $log_action->bind_param("ss", $file_data['file_name'], $staff_id);
    $log_action->execute();
    
    // 2. Force file download headers
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file_data['file_name']) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_data['stored_path']));
    
    // 3. Output the file content
    readfile($file_data['stored_path']);
    exit;
} else {
    // Log failure
    $log_action = $conn->prepare("INSERT INTO logs (action, file_name, staff_id) VALUES ('Download Failed/File Not Found', ?, ?)");
    $log_action->bind_param("ss", "ID: $file_id", $staff_id);
    $log_action->execute();
    
    die("Error: The requested file could not be found or accessed.");
}
?>