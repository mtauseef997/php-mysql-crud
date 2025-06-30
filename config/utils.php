<?php
/**
 * Utility Functions for Student Record Management System
 * Contains common functions used across the application
 */

/**
 * Calculate percentage from obtained and total marks
 * 
 * @param float $obtained Obtained marks
 * @param float $total Total marks
 * @param int $decimal Number of decimal places
 * @return float Calculated percentage
 */
function calculatePercentage($obtained, $total, $decimal = 2)
{
    return ($total == 0) ? 0 : round(($obtained / $total) * 100, $decimal);
}

/**
 * Get grade based on percentage
 * 
 * @param float $percent Percentage value
 * @return string Grade (A+, A, B, C, D, F)
 */
function getGrade($percent)
{
    if ($percent >= 90) return "A+";
    elseif ($percent >= 80) return "A";
    elseif ($percent >= 70) return "B";
    elseif ($percent >= 60) return "C";
    elseif ($percent >= 50) return "D";
    else return "F";
}

/**
 * Get remarks based on grade
 * 
 * @param string $grade Grade value
 * @return string Remarks
 */
function getRemarks($grade)
{
    return match ($grade) {
        'A+' => 'Excellent',
        'A'  => 'Great',
        'B'  => 'Good',
        'C'  => 'Satisfactory',
        'D'  => 'Needs Improvement',
        default => 'Work Hard'
    };
}

/**
 * Sanitize input data
 * 
 * @param string $data Input data
 * @return string Sanitized data
 */
function sanitizeInput($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Validate marks (should be between 0 and 100)
 * 
 * @param int $marks Marks to validate
 * @return bool True if valid, false otherwise
 */
function validateMarks($marks)
{
    return is_numeric($marks) && $marks >= 0 && $marks <= 100;
}

/**
 * Generate JSON response for API
 * 
 * @param bool $success Success status
 * @param string $message Response message
 * @param array $data Response data
 * @return void
 */
function jsonResponse($success, $message = '', $data = [])
{
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

/**
 * Get pagination info
 * 
 * @param int $totalRecords Total number of records
 * @param int $currentPage Current page number
 * @param int $recordsPerPage Records per page
 * @return array Pagination information
 */
function getPaginationInfo($totalRecords, $currentPage, $recordsPerPage)
{
    $totalPages = ceil($totalRecords / $recordsPerPage);
    $offset = ($currentPage - 1) * $recordsPerPage;
    
    return [
        'totalRecords' => $totalRecords,
        'totalPages' => $totalPages,
        'currentPage' => $currentPage,
        'recordsPerPage' => $recordsPerPage,
        'offset' => $offset,
        'hasNext' => $currentPage < $totalPages,
        'hasPrev' => $currentPage > 1
    ];
}
?>
