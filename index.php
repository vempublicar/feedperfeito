<?php
// Include session management
require_once 'config/session.php';

// Get the page parameter from the URL
$page = isset($_GET['page']) ? $_GET['page'] : '';
$section = isset($_GET['section']) ? $_GET['section'] : '';

// Determine if a user or admin session is active
$isUser = isUserLoggedIn();
$isAdmin = isAdminLoggedIn();

if ($isUser || $isAdmin) {
    if ($isAdmin && ($page === 'admin' || empty($page))) {
        // Prioritize admin if admin session is active and page is 'admin' or empty
        include 'dashboard-adm.php';
    } elseif ($isUser) {
        // Default to user dashboard if user session is active
        switch ($page) {
            case 'dashboard':
                include 'dashboard.php';
                break;
            case 'logout':
                include 'logout.php';
                break;
            default:
                include 'dashboard.php';
                break;
        }
    } else {
        // include 'login.php';
        // Fallback for unexpected states, redirect to login
        header('Location: ' . $_SESSION['base_url'] . '/login');
        exit();
    }
} else { // No user or admin is logged in
    switch ($page) {
        case 'login':
            include 'login.php';
            break;
        case 'register':
            include 'register.php';
            break;
        case 'index':
            include 'home.php';
            break;
        default:
            
            // Redirect to login page if not logged in and trying to access restricted pages
            if ($page === 'dashboard' || $page === 'admin') {
                // include 'login.php';
                header('Location: ' . $_SESSION['base_url'] . '/login');
                exit();
            }
            include 'home.php';
            break;
    }
}
?>