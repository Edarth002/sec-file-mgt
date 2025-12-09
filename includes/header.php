<?php
include_once __DIR__ . '/../db.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['staff_id']);
$is_admin = isset($_SESSION['is_super_admin']) && $_SESSION['is_super_admin'] == 1;
$current_page = basename($_SERVER['PHP_SELF']);
if(isset($file_path)) {
    $current_page = basename($file_path); // For files in subdirectories
}

// Logic to protect logged-in pages (e.g., if on index/login/register but already logged in)
if ($is_logged_in && ($current_page == 'index.php' || $current_page == 'login.php' || $current_page == 'register.php')) {
    header("Location: dashboard.php");
    exit;
}

// Logic to protect non-logged-in pages
if (!$is_logged_in && ($current_page != 'index.php' && $current_page != 'login.php' && $current_page != 'register.php')) {
    header("Location: index.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEC Document Repository - Nigeria</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<header class="header-nav">
    <div class="logo">
        <a href="dashboard.php">SEC Document Repository</a>
    </div>
    
    <?php if ($is_logged_in): ?>
        <nav class="nav-links">
            <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php' || $current_page == 'view_files.php') ? 'active' : '' ?>">View Documents</a>
            <a href="../upload_file.php" class="<?= $current_page == 'upload_file.php' ? 'active' : '' ?>">Upload File</a>
            
            <?php if ($is_admin): ?>
                <a href="/admin/add_staff.php" class="<?= $current_page == 'add_staff.php' || $current_page == 'view_logs.php' ? 'active' : '' ?>">Admin Panel</a>
            <?php endif; ?>
            
            <span>|</span>
            <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['username'] ?? 'Staff') ?>)</a>
        </nav>
    <?php else: ?>
        <nav class="nav-links">
            <a href="index.php">Login</a>
            <a href="register.php">Register</a>
        </nav>
    <?php endif; ?>
</header>
<main class="container">