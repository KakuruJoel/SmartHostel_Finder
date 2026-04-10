<?php
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

/**
 * 1. SESSION REGENERATION
 * Prevents "Session Hijacking" by changing the ID every 30 minutes
 */
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
} else {
    $interval = 60 * 30; // 30 minutes
    if (time() - $_SESSION['last_regeneration'] > $interval) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

/**
 * 2. INACTIVITY TIMEOUT
 * Automatically logs out user after 30 minutes of no movement
 */
$timeout = 1800; // 30 minutes in seconds

if (isset($_SESSION['last_activity'])) {
    $inactive = time() - $_SESSION['last_activity'];
    if ($inactive > $timeout) {
        session_unset();
        session_destroy();
        header("Location: auth.php?reason=timeout");
        exit();
    }
}
$_SESSION['last_activity'] = time(); // Reset clock on every page load

/**
 * 3. ROLE PROTECTION FUNCTION
 * Blocks unauthorized URL access
 */
function protect_page($allowed_roles = [])
{
    // Check if logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: auth.php?error=login_required");
        exit();
    }

    // Check if role is allowed (tenant, landlord, or admin)
    if (!empty($allowed_roles)) {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
            header("Location: dashboard.php?error=unauthorized_access");
            exit();
        }
    }
}

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
