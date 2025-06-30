<?php

/**
 * API Endpoint for Student Records
 * Handles AJAX requests for fetching, sorting, and filtering data
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../connect.php';

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            handleGetRequest($action, $con);
            break;
        case 'POST':
            handlePostRequest($action, $con);
            break;
        case 'DELETE':
            handleDeleteRequest($action, $con);
            break;
        default:
            jsonResponse(false, 'Method not allowed');
    }
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    jsonResponse(false, 'An error occurred. Please try again.');
}

/**
 * Handle GET requests
 */
function handleGetRequest($action, $con)
{
    switch ($action) {
        case 'list':
            getStudentsList($con);
            break;
        case 'get':
            getStudent($con);
            break;
        default:
            jsonResponse(false, 'Invalid action');
    }
}

/**
 * Handle POST requests
 */
function handlePostRequest($action, $con)
{
    switch ($action) {
        case 'create':
            createStudent($con);
            break;
        case 'update':
            updateStudent($con);
            break;
        default:
            jsonResponse(false, 'Invalid action');
    }
}

/**
 * Handle DELETE requests
 */
function handleDeleteRequest($action, $con)
{
    switch ($action) {
        case 'delete':
            deleteStudent($con);
            break;
        default:
            jsonResponse(false, 'Invalid action');
    }
}

/**
 * Get students list with pagination, sorting, and filtering
 */
function getStudentsList($con)
{
    // Get parameters
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = max(5, min(100, intval($_GET['limit'] ?? 10)));
    $search = $_GET['search'] ?? '';
    $sortColumn = $_GET['sort'] ?? 'ID';
    $sortDirection = strtoupper($_GET['direction'] ?? 'ASC');

    // Validate sort direction
    $sortDirection = in_array($sortDirection, ['ASC', 'DESC']) ? $sortDirection : 'ASC';

    // Validate sort column
    $allowedColumns = ['ID', 'NAME', 'ENG', 'URDU', 'MATHS', 'PHYSICS', 'CHEMISTRY', 'TOTAL', 'PERCENT', 'GRADE'];
    $sortColumn = in_array($sortColumn, $allowedColumns) ? $sortColumn : 'ID';

    // Build WHERE clause for search
    $whereClause = '';
    $params = [];
    $types = '';

    if (!empty($search)) {
        $whereClause = "WHERE NAME LIKE ? OR GRADE LIKE ? OR REMARKS LIKE ?";
        $searchParam = "%$search%";
        $params = [$searchParam, $searchParam, $searchParam];
        $types = 'sss';
    }

    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM record $whereClause";
    $countStmt = $con->prepare($countSql);

    if (!empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }

    $countStmt->execute();
    $totalRecords = $countStmt->get_result()->fetch_assoc()['total'];

    // Calculate pagination
    $pagination = getPaginationInfo($totalRecords, $page, $limit);

    // Get records
    $sql = "SELECT * FROM record $whereClause ORDER BY $sortColumn $sortDirection LIMIT ? OFFSET ?";
    $stmt = $con->prepare($sql);

    if (!empty($params)) {
        $params[] = $limit;
        $params[] = $pagination['offset'];
        $types .= 'ii';
        $stmt->bind_param($types, ...$params);
    } else {
        $stmt->bind_param('ii', $limit, $pagination['offset']);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $records = $result->fetch_all(MYSQLI_ASSOC);

    jsonResponse(true, 'Records fetched successfully', [
        'records' => $records,
        'pagination' => $pagination
    ]);
}

/**
 * Get single student record
 */
function getStudent($con)
{
    $id = intval($_GET['id'] ?? 0);

    if ($id <= 0) {
        jsonResponse(false, 'Invalid student ID');
    }

    $stmt = $con->prepare("SELECT * FROM record WHERE ID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($record = $result->fetch_assoc()) {
        jsonResponse(true, 'Student found', $record);
    } else {
        jsonResponse(false, 'Student not found');
    }
}

/**
 * Create new student record
 */
function createStudent($con)
{
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate input
    $name = sanitizeInput($input['name'] ?? '');
    $english = intval($input['english'] ?? 0);
    $urdu = intval($input['urdu'] ?? 0);
    $maths = intval($input['maths'] ?? 0);
    $physics = intval($input['physics'] ?? 0);
    $chemistry = intval($input['chemistry'] ?? 0);

    // Validate data
    if (empty($name)) {
        jsonResponse(false, 'Student name is required');
    }

    if (
        !validateMarks($english) || !validateMarks($urdu) || !validateMarks($maths) ||
        !validateMarks($physics) || !validateMarks($chemistry)
    ) {
        jsonResponse(false, 'All marks must be between 0 and 100');
    }

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
        jsonResponse(true, 'Student record created successfully', ['id' => $con->insert_id]);
    } else {
        jsonResponse(false, 'Failed to create student record');
    }
}

/**
 * Update student record
 */
function updateStudent($con)
{
    $input = json_decode(file_get_contents('php://input'), true);

    $id = intval($input['id'] ?? 0);
    if ($id <= 0) {
        jsonResponse(false, 'Invalid student ID');
    }

    // Validate input
    $name = sanitizeInput($input['name'] ?? '');
    $english = intval($input['english'] ?? 0);
    $urdu = intval($input['urdu'] ?? 0);
    $maths = intval($input['maths'] ?? 0);
    $physics = intval($input['physics'] ?? 0);
    $chemistry = intval($input['chemistry'] ?? 0);

    // Validate data
    if (empty($name)) {
        jsonResponse(false, 'Student name is required');
    }

    if (
        !validateMarks($english) || !validateMarks($urdu) || !validateMarks($maths) ||
        !validateMarks($physics) || !validateMarks($chemistry)
    ) {
        jsonResponse(false, 'All marks must be between 0 and 100');
    }

    // Calculate results
    $total = $english + $urdu + $maths + $physics + $chemistry;
    $percent = calculatePercentage($total, 500);
    $grade = getGrade($percent);
    $remarks = getRemarks($grade);

    // Update record
    $sql = "UPDATE record SET NAME=?, ENG=?, URDU=?, MATHS=?, PHYSICS=?, CHEMISTRY=?, TOTAL=?, PERCENT=?, GRADE=?, REMARKS=? WHERE ID=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("siiiiiddssi", $name, $english, $urdu, $maths, $physics, $chemistry, $total, $percent, $grade, $remarks, $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            jsonResponse(true, 'Student record updated successfully');
        } else {
            jsonResponse(false, 'No changes made or student not found');
        }
    } else {
        jsonResponse(false, 'Failed to update student record');
    }
}

/**
 * Delete student record
 */
function deleteStudent($con)
{
    $id = intval($_GET['id'] ?? 0);

    if ($id <= 0) {
        jsonResponse(false, 'Invalid student ID');
    }

    $stmt = $con->prepare("DELETE FROM record WHERE ID = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            jsonResponse(true, 'Student record deleted successfully');
        } else {
            jsonResponse(false, 'Student not found');
        }
    } else {
        jsonResponse(false, 'Failed to delete student record');
    }
}
