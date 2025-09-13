<?php
require_once 'config/session.php';

// Destroy any existing session
if (isUserLoggedIn()) {
    destroyUserSession();
} elseif (isAdminLoggedIn()) {
    destroyAdminSession();
}

// Redirect to login page
header('Location: ' . $_SESSION['base_url'] . '/login');
exit();
?>