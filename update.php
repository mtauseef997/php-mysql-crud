<?php

/**
 * Enhanced Update Student Record Page
 * Features: Better UI, validation, and error handling
 */
require_once 'connect.php';

// Get and validate ID
$id = isset($_GET['ID']) ? (int)$_GET['ID'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Fetch student record
$stmt = $con->prepare("SELECT * FROM `record` WHERE ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: index.php");
    exit();
}

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

        // Update record
        $stmt = $con->prepare("UPDATE `record` SET NAME=?, ENG=?, URDU=?, MATHS=?, PHYSICS=?, CHEMISTRY=?, TOTAL=?, PERCENT=?, GRADE=?, REMARKS=? WHERE ID=?");
        $stmt->bind_param("siiiiiddssi", $name, $english, $urdu, $maths, $physics, $chemistry, $total, $percent, $grade, $remarks, $id);

        if ($stmt->execute()) {
            $message = "Student record updated successfully!";
            $messageType = "success";

            // Update user array with new values for form display
            $user['NAME'] = $name;
            $user['ENG'] = $english;
            $user['URDU'] = $urdu;
            $user['MATHS'] = $maths;
            $user['PHYSICS'] = $physics;
            $user['CHEMISTRY'] = $chemistry;
        } else {
            $message = "Error updating record: " . $stmt->error;
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
    <title>Update Student Record - Student Management System</title>

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
                            <i class="fas fa-user-edit me-2"></i>
                            Update Student Record
                        </h3>
                        <p class="mb-0 mt-1 opacity-75">Edit student information and marks</p>
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
                    <input type="hidden" name="id" value="<?= $id ?>">

                    <div class="mb-4">
                        <label for="name" class="form-label">
                            <i class="fas fa-user me-2"></i>Student Name
                        </label>
                        <input type="text"
                            name="name"
                            class="form-control"
                            id="name"
                            placeholder="Enter student full name"
                            value="<?= htmlspecialchars($user['NAME']) ?>"
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
                                    value="<?= htmlspecialchars($user['ENG']) ?>"
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
                                    value="<?= htmlspecialchars($user['URDU']) ?>"
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
                                    value="<?= htmlspecialchars($user['MATHS']) ?>"
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
                                    value="<?= htmlspecialchars($user['PHYSICS']) ?>"
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
                                    value="<?= htmlspecialchars($user['CHEMISTRY']) ?>"
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
                            <i class="fas fa-save me-2"></i>Update Student
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