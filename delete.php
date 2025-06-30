<?php

/**
 * Enhanced Delete Student Record
 * Features: Better error handling and security
 */
require_once 'connect.php';

// Check if request is valid
if (!isset($_GET['ID']) || !is_numeric($_GET['ID'])) {
    header("Location: index.php?error=invalid_id");
    exit();
}

$id = (int)$_GET['ID'];

// Check if record exists
$checkStmt = $con->prepare("SELECT NAME FROM `record` WHERE ID = ?");
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php?error=not_found");
    exit();
}

$student = $result->fetch_assoc();

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $stmt = $con->prepare("DELETE FROM `record` WHERE ID = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: index.php?success=deleted");
        exit();
    } else {
        $error = "Error deleting record: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Student Record - Student Management System</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <!-- Header Card -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">
                            <i class="fas fa-trash-alt me-2"></i>
                            Delete Student Record
                        </h3>
                        <p class="mb-0 mt-1 opacity-75">Confirm deletion of student record</p>
                    </div>
                    <a href="index.php" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Confirmation Card -->
        <div class="card shadow">
            <div class="card-body text-center">
                <div class="mb-4">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                </div>

                <h4 class="mb-3">Are you sure you want to delete this record?</h4>

                <div class="alert alert-warning">
                    <strong>Student Name:</strong> <?= htmlspecialchars($student['NAME']) ?><br>
                    <strong>Student ID:</strong> <?= $id ?>
                </div>

                <p class="text-muted mb-4">
                    This action cannot be undone. The student record will be permanently removed from the system.
                </p>

                <form method="post" class="d-inline">
                    <input type="hidden" name="confirm_delete" value="1">
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Yes, Delete Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>