<?php

/**
 * SmartHostel FINDER - Core Configuration
 * 1. Secure Session Management
 * 2. Database Connection
 * 3. Global Security Filters
 * 4. Helper Functions
 */

// --- 1. SECURE SESSION SETTINGS ---
// We set these BEFORE session_start() to ensure cookies are tamper-proof
session_set_cookie_params([
    'lifetime' => 0,            // Session expires when browser closes
    'path' => '/',              // Available across the whole site
    'domain' => '',             // Default to current domain
    'secure' => false,          // Set to TRUE if using HTTPS
    'httponly' => true,         // Prevents JavaScript from stealing session ID
    'samesite' => 'Strict'      // Prevents CSRF attacks
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- 2. DATABASE CONNECTION ---
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "hostel_finder_db";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("CRITICAL ERROR: System could not connect to database. " . $conn->connect_error);
}

// --- 3. SESSION HIJACKING PROTECTION ---
/**
 * If a user is logged in, we check if their "Fingerprint" (Browser/User Agent) 
 * matches what they started with. If it changes, someone might be 
 * stealing their session cookie.
 */
if (isset($_SESSION['user_id'])) {
    // If it's a new login, store the User Agent
    if (!isset($_SESSION['user_agent'])) {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    }

    // Validate the Fingerprint
    if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        session_unset();
        session_destroy();
        header("Location: login.php?msg=security_alert");
        exit();
    }
}

// --- 4. GLOBAL HELPER FUNCTIONS ---

/**
 * Clean user input to prevent basic SQL Injection
 */
function clean($data)
{
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

/**
 * Format currency for Ugandan Shillings (UGX)
 */
function formatMoney($amount)
{
    return "UGX " . $amount;
}

/**
 * Check if the user has a specific role (Access Control)
 */
function restrictTo($role)
{
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header("Location: login.php?msg=unauthorized");
        exit();
    }
}

// --- 5. SYSTEM SETTINGS ---
$system_name = "SmartHostel FINDER";
$admin_email = "support@smarthostel.ug";
$logo_path = "project_logo.png";
