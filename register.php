<?php
$file_path = __FILE__;
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_id = $conn->real_escape_string($_POST['staff_id']);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // 1. Check if staff_id and email are approved
    $check_approved = $conn->prepare("SELECT full_name FROM approved_staff WHERE staff_id = ? AND email = ?");
    $check_approved->bind_param("ss", $staff_id, $email);
    $check_approved->execute();
    $approved_result = $check_approved->get_result();

    if ($approved_result->num_rows === 0) {
        $error = "Staff ID and Email combination not found or approved. Please contact Super Admin.";
    } else {
        // 2. Check if staff_id is already registered
        $check_user = $conn->prepare("SELECT id FROM users WHERE staff_id = ?");
        $check_user->bind_param("s", $staff_id);
        $check_user->execute();
        $user_result = $check_user->get_result();
        
        if ($user_result->num_rows > 0) {
            $error = "This Staff ID is already registered. Please login.";
        } else {
            // 3. Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_user = $conn->prepare("INSERT INTO users (staff_id, username, email, password) VALUES (?, ?, ?, ?)");
            $insert_user->bind_param("ssss", $staff_id, $username, $email, $hashed_password);

            if ($insert_user->execute()) {
                // Log the registration
                $log_action = $conn->prepare("INSERT INTO logs (action, staff_id) VALUES ('User Registered', ?)");
                $log_action->bind_param("s", $staff_id);
                $log_action->execute();
                
                $success = "Registration successful! You can now <a href='index.php'>log in</a>.";
            } else {
                $error = "Registration failed: " . $conn->error;
            }
        }
    }
}
include 'includes/header.php';
?>

<h2>Staff Registration</h2>
<?php if (isset($success)): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<form method="POST" action="register.php">
    <div class="form-group">
        <label for="staff_id">Staff ID (Must be pre-approved)</label>
        <input type="text" id="staff_id" name="staff_id" required>
    </div>
    <div class="form-group">
        <label for="email">SEC Email (Must match pre-approved record)</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="username">Choose a Username</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div class="form-group">
        <label for="password">Choose a Password</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="btn">Complete Registration</button>
</form>

<?php include 'includes/footer.php'; ?>