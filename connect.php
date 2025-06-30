<?php

/**
 * Database Configuration and Connection
 * Enhanced with error handling and configuration management
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'phpcrud');

// Create connection with error handling
$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($con->connect_error) {
    error_log("Database connection failed: " . $con->connect_error);
    die("Connection failed. Please try again later.");
}

// Set charset to UTF-8
$con->set_charset("utf8");

// Include utility functions
require_once __DIR__ . '/config/utils.php';
