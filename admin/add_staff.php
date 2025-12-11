<?php
$file_path = __FILE__;
include '../db.php';
include '../includes/restrict_admin.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_id = $conn->real_escape_string($_POST['new_staff_id']);
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    
    // Check if staff ID already exists
    $check_stmt = $conn->prepare("SELECT staff_id FROM approved_staff WHERE staff_id = ? OR email = ?");
    $check_stmt->bind_param("ss", $staff_id, $email);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        $error = "Staff ID or Email is already approved.";
    } else {
        // Insert new approved staff
        $insert_stmt = $conn->prepare("INSERT INTO approved_staff (staff_id, full_name, email) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("sss", $staff_id, $full_name, $email);
        
        if ($insert_stmt->execute()) {
            // Log the action
            $log_staff_id = $_SESSION['staff_id'];
            $log_action_text = "Approved new staff: $staff_id ($full_name)";
            $log_action = $conn->prepare("INSERT INTO logs (action, staff_id) VALUES (?, ?)");
            $log_action->bind_param("ss", $log_action_text, $log_staff_id);
            $log_action->execute();
            
            $success = "Staff ID **$staff_id** added successfully to the approved list.";
        } else {
            $error = "Error adding staff: " . $conn->error;
        }
    }
}
include '../includes/header.php';
?>

<h2>👤 Super Admin: Approve New Staff</h2>
<?php if (isset($success)): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>admin/add_staff.php">
    <div class="form-group">
        <label for="new_staff_id">New Staff ID</label>
        <input type="text" id="new_staff_id" name="new_staff_id" placeholder="e.g., FRN/SEC/99999" required>
    </div>
    <div class="form-group">
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" required>
    </div>
    <div class="form-group">
        <label for="email">SEC Email</label>
        <input type="email" id="email" name="email" required>
    </div>
    <button type="submit" class="btn">Approve Staff ID</button>
</form>

<h3 style="margin-top: 30px;">Admin Actions</h3>
<p><a href="<?= BASE_URL ?>admin/view_logs.php" class="btn btn-secondary">View System Logs</a></p>

<?php include '../includes/footer.php'; ?>