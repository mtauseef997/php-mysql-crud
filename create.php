<?php
include "connect.php";

// Function to calculate percentage
function calculatePercentage($obtained, $total, $decimal = 2)
{
    return ($total == 0) ? 0 : round(($obtained / $total) * 100, $decimal);
}

// Function to get grade
function getGrade($percent)
{
    if ($percent >= 90) return "A+";
    elseif ($percent >= 80) return "A";
    elseif ($percent >= 70) return "B";
    elseif ($percent >= 60) return "C";
    elseif ($percent >= 50) return "D";
    else return "F";
}

// Function to get remarks
function getRemarks($grade)
{
    switch ($grade) {
        case 'A+':
            return "Excellent";
        case 'A':
            return "Great";
        case 'B':
            return "Good";
        case 'C':
            return "Satisfactory";
        case 'D':
            return "Needs Improvement";
        default:
            return "Work Hard";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $english = isset($_POST['eng']) ? (int)$_POST['eng'] : 0;
    $urdu = isset($_POST['urdu']) ? (int)$_POST['urdu'] : 0;
    $maths = isset($_POST['maths']) ? (int)$_POST['maths'] : 0;
    $physics = isset($_POST['physics']) ? (int)$_POST['physics'] : 0;
    $chemistry = isset($_POST['chemistry']) ? (int)$_POST['chemistry'] : 0;

    $total = $english + $urdu + $maths + $physics + $chemistry;
    $percent = calculatePercentage($total, 500);
    $grade = getGrade($percent);
    $remarks = getRemarks($grade);

    $sql = "INSERT INTO record (NAME, ENG, URDU, MATHS, PHYSICS, CHEMISTRY, TOTAL, PERCENT, GRADE, REMARKS) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("siiiiiddss", $name, $english, $urdu, $maths, $physics, $chemistry, $total, $percent, $grade, $remarks);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success text-center'>Record inserted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Error: " . $stmt->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Result Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Student Result Entry</h3>
                <a href="index.php" class="btn btn-dark btn-sm">Back to List</a>
            </div>

            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Student Name</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Enter student name"
                            required>
                    </div>

                    <h5 class="text-secondary mt-4 mb-3">Enter Marks (Out of 100)</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="eng" class="form-label">English</label>
                            <input type="number" name="eng" class="form-control" id="eng" min="0" max="100" required>
                        </div>
                        <div class="col-md-4">
                            <label for="urdu" class="form-label">Urdu</label>
                            <input type="number" name="urdu" class="form-control" id="urdu" min="0" max="100" required>
                        </div>
                        <div class="col-md-4">
                            <label for="maths" class="form-label">Maths</label>
                            <input type="number" name="maths" class="form-control" id="maths" min="0" max="100"
                                required>
                        </div>
                        <div class="col-md-4">
                            <label for="physics" class="form-label">Physics</label>
                            <input type="number" name="physics" class="form-control" id="physics" min="0" max="100"
                                required>
                        </div>
                        <div class="col-md-4">
                            <label for="chemistry" class="form-label">Chemistry</label>
                            <input type="number" name="chemistry" class="form-control" id="chemistry" min="0" max="100"
                                required>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-4">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>