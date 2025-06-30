<?php
include 'connect.php';

$limit = 10; // Records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Get total records count
$totalResult = $con->query("SELECT COUNT(*) AS count FROM `record`");
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['count'];
$totalPages = ceil($totalRecords / $limit);

// Fetch records for current page
$result = $con->query("SELECT * FROM `record` LIMIT $limit OFFSET $offset");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Student Record List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
</head>

<body>
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Student Record List</h3>
                <a href="create.php" class="btn btn-dark">Add Student</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="studentTable"
                        class="table table-bordered table-striped table-hover text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>English</th>
                                <th>Urdu</th>
                                <th>Maths</th>
                                <th>Physics</th>
                                <th>Chemistry</th>
                                <th>Obtained</th>
                                <th>Total</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result && $result->num_rows > 0) {
                                $records = $result->fetch_all(MYSQLI_ASSOC);
                                foreach ($records as $row) {
                                    echo "<tr>
                                        <td>{$row['ID']}</td>
                                        <td>{$row['NAME']}</td>
                                        <td>{$row['ENG']}</td>
                                        <td>{$row['URDU']}</td>
                                        <td>{$row['MATHS']}</td>
                                        <td>{$row['PHYSICS']}</td>
                                        <td>{$row['CHEMISTRY']}</td>
                                        <td>{$row['TOTAL']}</td>
                                        <td>500</td>
                                        <td>{$row['PERCENT']}%</td>
                                        <td>{$row['GRADE']}</td>
                                        <td>{$row['REMARKS']}</td>
                                        <td>
                                            <a href='update.php?ID={$row['ID']}' class='btn btn-sm btn-warning mb-1'>Edit</a><br>
                                            <a href='delete.php?ID={$row['ID']}' class='btn btn-sm btn-danger'>Delete</a>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='13'>No records found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- PHP Pagination -->
                <nav>
                    <ul class="pagination justify-content-center mt-3">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                        </li>
                        <?php endif; ?>

                        <?php
                        for ($i = 1; $i <= $totalPages; $i++) {
                            $active = ($i == $page) ? 'active' : '';
                            echo "<li class='page-item $active'><a class='page-link' href='?page=$i'>$i</a></li>";
                        }
                        ?>

                        <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>

            </div>
        </div>
    </div>

    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Initialize DataTable: disable pagination because we handle it in PHP -->
    <script>
    $(document).ready(function() {
        $('#studentTable').DataTable({
            paging: false, // Disable DataTables pagination
            lengthChange: false, // Hide "show N entries"
            searching: true, // Enable search/filter
            ordering: true // Enable sorting
        });
    });
    </script>
</body>

</html>