<?php
$file_path = __FILE__; // Pass file path for header
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $staff_id = $conn->real_escape_string($_POST['staff_id']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, is_super_admin FROM users WHERE staff_id = ?");
    $stmt->bind_param("s", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Success: Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['staff_id'] = $staff_id;
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_super_admin'] = $user['is_super_admin'];

            // Log successful login
            $log_action = $conn->prepare("INSERT INTO logs (action, staff_id) VALUES ('Login Success', ?)");
            $log_action->bind_param("s", $staff_id);
            $log_action->execute();
            
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid Staff ID or Password.";
        }
    } else {
        $error = "Invalid Staff ID or Password.";
    }
}
include 'includes/header.php';
?>

<h2>Staff Login</h2>
<?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<form method="POST" action="index.php">
    <div class="form-group">
        <label for="staff_id">Staff ID (e.g., FRN/SEC/12345)</label>
        <input type="text" id="staff_id" name="staff_id" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" name="login" class="btn">Login Securely</button>
</form>

<p style="margin-top: 20px;">
    New staff? <a href="register.php" style="color: var(--sec-green-light); text-decoration: none; font-weight: 600;">Register here</a>.
</p>

<?php include 'includes/footer.php'; ?>