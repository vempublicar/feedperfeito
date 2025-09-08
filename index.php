<?php
// Include session management
require_once 'config/session.php';

// Get the page parameter from the URL
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$section = isset($_GET['section']) ? $_GET['section'] : '';

// Handle routing
switch ($page) {
    case 'login':
        include 'login.php';
        break;
        
    case 'register':
        include 'register.php';
        break;
        
    case 'dashboard':
        // Require user login for dashboard
        requireUserLogin();
        include 'dashboard.php';
        break;
        
    case 'admin':
        // Require admin login for admin panel
        requireAdminLogin();
        include 'dashboard-adm.php';
        break;
        
    default:
        // Default to dashboard if page not found
        requireUserLogin();
        include 'dashboard.php';
        break;
}
?>