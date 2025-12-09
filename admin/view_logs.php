<?php
$file_path = __FILE__;
include '../db.php';
include '../includes/restrict_admin.php'; 

// Fetch all logs
$logs_query = "SELECT * FROM logs ORDER BY action_time DESC";
$logs_result = $conn->query($logs_query);

include '../includes/header.php';
?>

<h2>📜 System Activity Logs</h2>

<p>This table records all major system actions, including logins, uploads, downloads, and administrative changes.</p>

<table class="data-table">
    <thead>
        <tr>
            <th>Time</th>
            <th>Staff ID</th>
            <th>Action</th>
            <th>Affected File/Detail</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($logs_result->num_rows > 0): ?>
        <?php while ($log = $logs_result->fetch_assoc()): ?>
            <tr>
                <td><?= date('Y-m-d H:i:s', strtotime($log['action_time'])) ?></td>
                <td><?= htmlspecialchars($log['staff_id'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($log['action'] ?? '') ?></td>
                <td><?= htmlspecialchars($log['file_name'] ?? '') ?></td> </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="4">No system logs found yet.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>