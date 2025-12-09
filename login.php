<?php
session_start();
require 'db.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT staff_id, username, password, is_super_admin FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute(); $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = [
                'staff_id' => $row['staff_id'],
                'username' => $row['username'],
                'is_super' => (bool)$row['is_super_admin']
            ];
            header("Location: ./dashboard.php"); exit;
        }
    }
    $msg = "Invalid credentials.";
}
?>
<!doctype html>
<html>
<head>
 <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700;800&display=swap"
      rel="stylesheet"
    />  
<meta charset="utf-8"><title>Login</title><link rel="stylesheet" href="./styles.css"></head>
<body>
  <div class="container" style="padding:3rem 1rem">
    
    <div class="card fade-in" style="max-width:420px;margin:0 auto;padding:1.4rem;background:rgba(255,255,255,0.9);border-radius:12px">
      <h2 style="margin:0 0 1rem">Secure Login</h2>
      <form method="post">
        <input name="username" type="text" placeholder="Username" required>
        <input name="password" type="password" placeholder="Password" required>
        <button class="primary" type="submit" style="margin-top:1rem">Login</button>
        <?php if($msg): ?><div class="error"><?php echo htmlspecialchars($msg) ?></div><?php endif; ?>
      </form>
      <p style="margin-top:.85rem">Need an account? Ask admin to add your staff ID to the approved list, then use <a href="./signup.php">Signup</a>.</p>
    </div>
  </div>
</body>
</html>
