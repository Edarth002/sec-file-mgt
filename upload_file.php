<?php
$file_path = __FILE__;
include 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: index.php");
    exit;
}

$categories = ['Policy Documents', 'Financial Reports', 'Legal Filings', 'Internal Memos', 'Other'];
$max_file_size = 10 * 1024 * 1024; // 10MB limit

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['document'])) {
    $category = $conn->real_escape_string($_POST['category']);
    $tags = $conn->real_escape_string($_POST['tags']);
    $notes = $conn->real_escape_string($_POST['notes']);
    $uploaded_by = $_SESSION['staff_id'];

    if (!in_array($category, $categories)) {
        $error = "Invalid category selected.";
    } elseif ($_FILES['document']['size'] > $max_file_size) {
        $error = "File is too large. Max size is 10MB.";
    } elseif ($_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        $error = "File upload error: " . $_FILES['document']['error'];
    } else {
        $file = $_FILES['document'];
        $original_file_name = $conn->real_escape_string($file['name']);
        
        // Generate a secure, unique name for storage
        $extension = pathinfo($original_file_name, PATHINFO_EXTENSION);
        $stored_name = uniqid('sec_') . '.' . $extension;
        $stored_path = 'uploads/' . $stored_name;

        // Move the uploaded file
        if (move_uploaded_file($file['tmp_name'], $stored_path)) {
            // Insert file metadata into the database
            $insert_stmt = $conn->prepare("INSERT INTO documents (file_name, stored_name, stored_path, category, tags, notes, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("sssssss", $original_file_name, $stored_name, $stored_path, $category, $tags, $notes, $uploaded_by);
            
            if ($insert_stmt->execute()) {
                // Log the upload
                $log_action = $conn->prepare("INSERT INTO logs (action, file_name, staff_id) VALUES ('File Uploaded', ?, ?)");
                $log_action->bind_param("ss", $original_file_name, $uploaded_by);
                $log_action->execute();
                
                $success = "File **$original_file_name** uploaded successfully!";
            } else {
                // Remove file if DB insert fails
                unlink($stored_path);
                $error = "Error saving file details: " . $conn->error;
            }
        } else {
            $error = "Error moving uploaded file. Check 'uploads/' folder permissions.";
        }
    }
}
include 'includes/header.php';
?>

<h2>📤 Secure Document Upload</h2>
<?php if (isset($success)): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<form method="POST" action="upload_file.php" enctype="multipart/form-data">
    <div class="form-group">
        <label for="document">Choose Document (Max 10MB)</label>
        <input type="file" id="document" name="document" required>
    </div>
    <div class="form-group">
        <label for="category">Category</label>
        <select id="category" name="category" required>
            <option value="">-- Select Category --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat ?>"><?= $cat ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="tags">Tags (Comma-separated keywords for easy search, e.g., 2024, AGM, quarterly)</label>
        <input type="text" id="tags" name="tags" placeholder="e.g., policy, 2024, compliance">
    </div>
    <div class="form-group">
        <label for="notes">Notes/Summary</label>
        <textarea id="notes" name="notes" rows="3"></textarea>
    </div>
    <button type="submit" class="btn">Upload Document</button>
</form>

<?php include 'includes/footer.php'; ?>