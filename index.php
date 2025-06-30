<?php

/**
 * Enhanced Student Record Management System - Main Index Page
 * Features: AJAX-based sorting, filtering, pagination, loader, and modern UI
 */
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Record Management System</title>

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
    <!-- Alert Container -->
    <div id="alertContainer" class="container mt-3"></div>

    <div class="container mt-4">
        <!-- Header Card -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">
                            <i class="fas fa-graduation-cap me-2"></i>
                            Student Record Management
                        </h3>
                        <p class="mb-0 mt-1 opacity-75">Manage and track student academic records</p>
                    </div>
                    <a href="create.php" class="btn btn-light">
                        <i class="fas fa-plus me-2"></i>Add Student
                    </a>
                </div>
            </div>
        </div>

        <!-- Controls Section -->
        <div class="controls-section">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="searchInput" class="form-control"
                            placeholder="Search by name, grade, or remarks...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="recordsPerPage" class="form-select">
                        <option value="5">5 records</option>
                        <option value="10" selected>10 records</option>
                        <option value="25">25 records</option>
                        <option value="50">50 records</option>
                        <option value="100">100 records</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div id="recordsInfo" class="text-muted small"></div>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="card shadow">
            <div class="card-body p-0">
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover text-center align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="sortable" data-column="ID">
                                        ID
                                    </th>
                                    <th class="sortable" data-column="NAME">
                                        Name
                                    </th>
                                    <th class="sortable" data-column="ENG">
                                        English
                                    </th>
                                    <th class="sortable" data-column="URDU">
                                        Urdu
                                    </th>
                                    <th class="sortable" data-column="MATHS">
                                        Maths
                                    </th>
                                    <th class="sortable" data-column="PHYSICS">
                                        Physics
                                    </th>
                                    <th class="sortable" data-column="CHEMISTRY">
                                        Chemistry
                                    </th>
                                    <th class="sortable" data-column="TOTAL">
                                        Obtained
                                    </th>
                                    <th>Total</th>
                                    <th class="sortable" data-column="PERCENT">
                                        Percentage
                                    </th>
                                    <th class="sortable" data-column="GRADE">
                                        Grade
                                    </th>
                                    <th>Remarks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentsTableBody">
                                <!-- Data will be loaded via AJAX -->
                                <tr>
                                    <td colspan="13" class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Loading student records...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            <nav id="pagination"></nav>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="assets/js/app.js"></script>
</body>

</html>