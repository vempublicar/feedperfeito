<?php
// Session management for the application

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set session timeout (30 minutes)
define('SESSION_TIMEOUT', 1800);

// Function to regenerate session ID
function regenerateSession() {
    if (session_status() == PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

// Function to set user session
function setUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_credits'] = $user['credits'];
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
    regenerateSession();
}

// Function to set admin session
function setAdminSession($admin) {
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['admin_role'] = $admin['role'];
    $_SESSION['admin_login_time'] = time();
    $_SESSION['admin_last_activity'] = time();
    regenerateSession();
}

// Function to check if user is logged in
function isUserLoggedIn() {
    if (isset($_SESSION['user_id']) && isset($_SESSION['last_activity'])) {
        // Check if session has expired
        if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
            // Session expired, destroy it
            destroyUserSession();
            return false;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    return false;
}

// Function to check if admin is logged in
function isAdminLoggedIn() {
    if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_last_activity'])) {
        // Check if session has expired
        if (time() - $_SESSION['admin_last_activity'] > SESSION_TIMEOUT) {
            // Session expired, destroy it
            destroyAdminSession();
            return false;
        }
        
        // Update last activity
        $_SESSION['admin_last_activity'] = time();
        return true;
    }
    
    return false;
}

// Function to get current user
function getCurrentUser() {
    if (isUserLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'name' => $_SESSION['user_name'],
            'credits' => $_SESSION['user_credits']
        ];
    }
    
    return null;
}

// Function to get current admin
function getCurrentAdmin() {
    if (isAdminLoggedIn()) {
        return [
            'id' => $_SESSION['admin_id'],
            'email' => $_SESSION['admin_email'],
            'name' => $_SESSION['admin_name'],
            'role' => $_SESSION['admin_role']
        ];
    }
    
    return null;
}

// Function to destroy user session
function destroyUserSession() {
    // Remove all session variables
    unset($_SESSION['user_id']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_credits']);
    unset($_SESSION['login_time']);
    unset($_SESSION['last_activity']);
    
    // Destroy the session
    session_destroy();
}

// Function to destroy admin session
function destroyAdminSession() {
    // Remove all session variables
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_role']);
    unset($_SESSION['admin_login_time']);
    unset($_SESSION['admin_last_activity']);
    
    // Destroy the session
    session_destroy();
}

// Function to require user login
function requireUserLogin() {
    if (!isUserLoggedIn()) {
        header('Location: /login');
        exit();
    }
}

// Function to require admin login
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: /login');
        exit();
    }
}

// Function to update user credits in session
function updateUserCreditsInSession($credits) {
    if (isUserLoggedIn()) {
        $_SESSION['user_credits'] = $credits;
    }
}
?>