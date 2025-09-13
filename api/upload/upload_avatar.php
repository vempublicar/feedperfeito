<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';

// Check if user is logged in
if (!isUserLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar_upload'])) {
    $file = $_FILES['avatar_upload'];

    // Basic file validation
    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'File upload error: ' . $file['error']]);
        exit();
    }

    $allowed_types = ['image/jpeg', 'image/png'];
    if (!in_array($file['type'], $allowed_types)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF are allowed.']);
        exit();
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit.']);
        exit();
    }

    // Supabase Storage configuration
    $supabaseUrl = trim(getenv('SUPABASE_URL'));
    $supabaseKey = trim(getenv('SUPABASE_KEY')); // Use SUPABASE_KEY for Storage operations
    $bucketName = 'avatars'; // Replace with your Supabase storage bucket name

    // Generate a unique file name
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = $user_id . '_' . uniqid() . '.' . $fileExtension;
    $filePath = $bucketName . '/' . $fileName;

    // Upload file to Supabase Storage
    $uploadUrl = $supabaseUrl . '/storage/v1/object/public/' . $filePath;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uploadUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file['tmp_name']));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $supabaseKey,
        'Content-Type: ' . $file['type'],
        'x-upsert: true' // Overwrite if file exists
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        error_log("Supabase Storage cURL Error: " . $error);
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to upload to storage: ' . $error]);
        exit();
    }

    if ($httpCode >= 400) {
        error_log("Supabase Storage HTTP Error (" . $httpCode . "): " . $response);
        http_response_code($httpCode);
        echo json_encode(['success' => false, 'message' => 'Failed to upload to storage: ' . $response]);
        exit();
    }

    // Get public URL of the uploaded file
    $publicUrl = $supabaseUrl . '/storage/v1/object/public/' . $filePath;

    // Update user's profile with avatar_url
    require_once __DIR__ . '/../../models/User.php';
    $userModel = new User();
    $updateResult = $userModel->update($user_id, ['avatar_url' => $publicUrl]);

    if ($updateResult) {
        echo json_encode(['success' => true, 'message' => 'Avatar uploaded and profile updated successfully!', 'avatar_url' => $publicUrl]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update user profile with avatar URL.']);
    }

} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed or no file uploaded.']);
}
?>