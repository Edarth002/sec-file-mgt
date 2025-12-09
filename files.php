<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }
$user = $_SESSION['user'];

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$sql = "SELECT * FROM documents WHERE 1=1";
$params = []; $types = "";
if ($keyword !== '') {
    $sql .= " AND (file_name LIKE ? OR tags LIKE ? OR notes LIKE ?)";
    $kw = "%$keyword%";
    $params[] = $kw; $params[] = $kw; $params[] = $kw;
    $types .= "sss";
}
if ($category !== '') {
    $sql .= " AND category = ?";
    $params[] = $category; $types .= "s";
}
$sql .= " ORDER BY uploaded_at DESC";
$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute(); $res = $stmt->get_result();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Documents</title><link rel="stylesheet" href="styles.css"></head>
<body>
<nav class="container"><div class="logo">SEC</div><div><a href="dashboard.php">Dashboard</a><a href="upload.php">Upload</a><a href="logout.php">Logout</a></div></nav>

<main class="container" style="padding:2rem 0">
  <section class="files">
    <h2>Search & Filter Documents</h2>
    <form method="get" style="display:flex;gap:.6rem;justify-content:center;margin-bottom:1rem">
      <input name="q" placeholder="Search by name, tag or note" value="<?php echo htmlspecialchars($keyword) ?>">
      <select name="category">
        <option value="">All Categories</option>
        <option<?php if($category=='Financials') echo ' selected';?>>Financials</option>
        <option<?php if($category=='Internal Records') echo ' selected';?>>Internal Records</option>
        <option<?php if($category=='Personnel Files') echo ' selected';?>>Personnel Files</option>
        <option<?php if($category=='Legal Documents') echo ' selected';?>>Legal Documents</option>
        <option<?php if($category=='Other') echo ' selected';?>>Other</option>
      </select>
      <button class="primary" type="submit">Search</button>
    </form>

    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Name</th><th>Category</th><th>Tags</th><th>Uploaded</th><th>By</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php while($row = $res->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['file_name']) ?></td>
              <td><?php echo htmlspecialchars($row['category']) ?></td>
              <td><?php echo htmlspecialchars($row['tags']) ?></td>
              <td><?php echo htmlspecialchars($row['uploaded_at']) ?></td>
              <td><?php echo htmlspecialchars($row['uploaded_by']) ?></td>
              <td class="actions">
                <a href="preview.php?id=<?php echo $row['id'] ?>">Preview</a>
                <a href="download.php?id=<?php echo $row['id'] ?>">Download</a>
                <a href="delete.php?id=<?php echo $row['id'] ?>" onclick="return confirm('Delete this file?')">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
          <?php if ($res->num_rows === 0): ?>
            <tr><td colspan="6" style="text-align:center;color:var(--muted)">No documents found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
</body>
</html>
