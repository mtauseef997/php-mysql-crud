<?php

/**
 * Enhanced Create Student Record Page
 * Features: Better UI, validation, and user experience
 */
require_once 'connect.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $name = sanitizeInput($_POST['name'] ?? '');
    $english = intval($_POST['eng'] ?? 0);
    $urdu = intval($_POST['urdu'] ?? 0);
    $maths = intval($_POST['maths'] ?? 0);
    $physics = intval($_POST['physics'] ?? 0);
    $chemistry = intval($_POST['chemistry'] ?? 0);

    // Validation
    $errors = [];

    if (empty($name)) {
        $errors[] = "Student name is required";
    }

    if (!validateMarks($english)) $errors[] = "English marks must be between 0 and 100";
    if (!validateMarks($urdu)) $errors[] = "Urdu marks must be between 0 and 100";
    if (!validateMarks($maths)) $errors[] = "Maths marks must be between 0 and 100";
    if (!validateMarks($physics)) $errors[] = "Physics marks must be between 0 and 100";
    if (!validateMarks($chemistry)) $errors[] = "Chemistry marks must be between 0 and 100";

    if (empty($errors)) {
        // Calculate results
        $total = $english + $urdu + $maths + $physics + $chemistry;
        $percent = calculatePercentage($total, 500);
        $grade = getGrade($percent);
        $remarks = getRemarks($grade);

        // Insert record
        $sql = "INSERT INTO record (NAME, ENG, URDU, MATHS, PHYSICS, CHEMISTRY, TOTAL, PERCENT, GRADE, REMARKS)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("siiiiiddss", $name, $english, $urdu, $maths, $physics, $chemistry, $total, $percent, $grade, $remarks);

        if ($stmt->execute()) {
            $message = "Student record created successfully!";
            $messageType = "success";
            // Clear form data
            $name = $english = $urdu = $maths = $physics = $chemistry = '';
        } else {
            $message = "Error creating record: " . $stmt->error;
            $messageType = "danger";
        }
    } else {
        $message = implode('<br>', $errors);
        $messageType = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student Record - Student Management System</title>

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
                            <i class="fas fa-user-plus me-2"></i>
                            Add Student Record
                        </h3>
                        <p class="mb-0 mt-1 opacity-75">Enter student information and marks</p>
                    </div>
                    <a href="index.php" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="card shadow">
            <div class="card-body">
                <form id="studentForm" method="post" novalidate>
                    <div class="mb-4">
                        <label for="name" class="form-label">
                            <i class="fas fa-user me-2"></i>Student Name
                        </label>
                        <input type="text"
                            name="name"
                            class="form-control"
                            id="name"
                            placeholder="Enter student full name"
                            value="<?= htmlspecialchars($name ?? '') ?>"
                            required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-secondary mb-3">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Subject Marks (Out of 100)
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="eng" class="form-label">English</label>
                                <input type="number"
                                    name="eng"
                                    class="form-control"
                                    id="eng"
                                    min="0"
                                    max="100"
                                    placeholder="0-100"
                                    value="<?= htmlspecialchars($english ?? '') ?>"
                                    required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="urdu" class="form-label">Urdu</label>
                                <input type="number"
                                    name="urdu"
                                    class="form-control"
                                    id="urdu"
                                    min="0"
                                    max="100"
                                    placeholder="0-100"
                                    value="<?= htmlspecialchars($urdu ?? '') ?>"
                                    required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="maths" class="form-label">Mathematics</label>
                                <input type="number"
                                    name="maths"
                                    class="form-control"
                                    id="maths"
                                    min="0"
                                    max="100"
                                    placeholder="0-100"
                                    value="<?= htmlspecialchars($maths ?? '') ?>"
                                    required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="physics" class="form-label">Physics</label>
                                <input type="number"
                                    name="physics"
                                    class="form-control"
                                    id="physics"
                                    min="0"
                                    max="100"
                                    placeholder="0-100"
                                    value="<?= htmlspecialchars($physics ?? '') ?>"
                                    required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="chemistry" class="form-label">Chemistry</label>
                                <input type="number"
                                    name="chemistry"
                                    class="form-control"
                                    id="chemistry"
                                    min="0"
                                    max="100"
                                    placeholder="0-100"
                                    value="<?= htmlspecialchars($chemistry ?? '') ?>"
                                    required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="assets/js/app.js"></script>
</body>

</html>