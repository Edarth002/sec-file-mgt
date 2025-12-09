<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user']) || !$_SESSION['user']['is_super']) { header("Location: login.php"); exit; }
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff = trim($_POST['staff_id']);
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $ins = $conn->prepare("INSERT INTO approved_staff (staff_id, full_name, email) VALUES (?,?,?)");
    $ins->bind_param("sss",$staff,$name,$email);
    if ($ins->execute()) $msg = "Staff added.";
    else $msg = "Failed to add (maybe exists).";
}
$all = $conn->query("SELECT * FROM approved_staff ORDER BY id DESC");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Staff Control</title><link rel="stylesheet" href="styles.css"></head>
<body>
<nav class="container"><div class="logo">SEC</div><div><a href="dashboard.php">Dashboard</a><a href="logout.php">Logout</a></div></nav>
<main class="container" style="padding:2rem 0">
  <?php if($msg): ?><div class="notice success"><?php echo htmlspecialchars($msg) ?></div><?php endif; ?>

  <section style="max-width:800px;margin:0 auto">
    <h2>Add Approved Staff</h2>
    <form method="post" style="display:flex;flex-direction:column;gap:.6rem">
      <input name="staff_id" placeholder="Staff ID (e.g. FRN/SEC/01006)" required>
      <input name="full_name" placeholder="Full name" required>
      <input name="email" placeholder="Email" required type="email">
      <button class="primary" type="submit">Add Staff</button>
    </form>

    <h3 style="margin-top:1.4rem">Approved Staff</h3>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Staff ID</th><th>Name</th><th>Email</th></tr></thead>
        <tbody>
          <?php while($r = $all->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($r['staff_id']) ?></td>
              <td><?php echo htmlspecialchars($r['full_name']) ?></td>
              <td><?php echo htmlspecialchars($r['email']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
</body>
</html>
