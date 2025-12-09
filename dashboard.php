<?php
$file_path = __FILE__;
include 'db.php';

// Define categories for search/filter dropdown
$categories = ['Policy Documents', 'Financial Reports', 'Legal Filings', 'Internal Memos', 'Other'];

// --- Handle Search/Filter Logic (Feature 5) ---
$search_category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$search_term = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "SELECT id, file_name, category, tags, uploaded_by, uploaded_at FROM documents WHERE 1=1";
$params = [];
$types = '';

if (!empty($search_category) && in_array($search_category, $categories)) {
    $sql .= " AND category = ?";
    $params[] = $search_category;
    $types .= 's';
}

if (!empty($search_term)) {
    $sql .= " AND (file_name LIKE ? OR tags LIKE ?)";
    $search_like = '%' . $search_term . '%';
    $params[] = $search_like;
    $params[] = $search_like;
    $types .= 'ss';
}

$stmt = $conn->prepare($sql . " ORDER BY uploaded_at DESC");

if (!empty($params)) {
    // Dynamically bind parameters
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$documents_result = $stmt->get_result();

include 'includes/header.php';
?>

<h2>📊 Document Repository Dashboard</h2>

<p>Welcome, **<?= htmlspecialchars($_SESSION['username'] ?? 'Staff') ?>** (<?= htmlspecialchars($_SESSION['staff_id'] ?? 'N/A') ?>). Use the search tool to find documents.</p>

<form method="GET" action="dashboard.php" style="display: flex; gap: 10px; margin-bottom: 20px;">
    <select name="category" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
        <option value="">-- All Categories --</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat ?>" <?= $search_category == $cat ? 'selected' : '' ?>><?= $cat ?></option>
        <?php endforeach; ?>
    </select>
    <input type="text" name="search" placeholder="Search by File Name or Tags..." value="<?= htmlspecialchars($search_term) ?>" style="flex-grow: 1;">
    <button type="submit" class="btn" style="padding: 8px 15px;">Search</button>
    <a href="dashboard.php" class="btn btn-secondary" style="padding: 8px 15px;">Reset</a>
</form>

<h3>Found Documents (<?= $documents_result->num_rows ?>)</h3>
<table class="data-table">
    <thead>
        <tr>
            <th>File Name</th>
            <th>Category</th>
            <th>Tags</th>
            <th>Uploaded By</th>
            <th>Uploaded On</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($doc = $documents_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($doc['file_name']) ?></td>
                <td><?= htmlspecialchars($doc['category']) ?></td>
                <td><?= htmlspecialchars($doc['tags']) ?></td>
                <td><?= htmlspecialchars($doc['uploaded_by']) ?></td>
                <td><?= date('Y-m-d H:i', strtotime($doc['uploaded_at'])) ?></td>
                <td>
                    <a href="download.php?id=<?= $doc['id'] ?>" class="btn" style="padding: 5px 10px;">Download</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>