<?php

session_start();

require_once __DIR__ . '/functions/user_functions.php';
require_once __DIR__ . '/functions/file_functions.php';
require_once __DIR__ . '/functions/folder_functions.php';
require_once __DIR__ . '/functions/helpers.php';

$info = [
    'success' => false,
    'LOGGED_IN' => is_logged_in(),
    'name' => $_SESSION['RAIN_USER']['name'] ?? 'User',
    'email' => $_SESSION['RAIN_USER']['email'] ?? 'Email',
    'data_type' => $_POST['data_type'] ?? '',
    'space_occupied' => get_occupied_space($_SESSION['RAIN_USER']['id'] ?? 0),
    'space_total' => 2, // Total GBs
    'breadcrumbs' => [],
];

$logged_in = $info['LOGGED_IN'];
// Handle cases where login is required
$without_login = ['user_signup', 'user_login'];
if(!$info['LOGGED_IN'] && (!in_array($info['data_type'], $without_login))) {
    $response = json_encode($info);
    echo $response;
    die;
}
// Process POST requests
if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['data_type'])) {
    $info['data_type'] = $_POST['data_type'];

    switch ($info['data_type']) {
        case 'upload_files':
            return handleFileUpload($info);  // Upload
        case 'get_files':
            return handleGetFiles($info);  // Get Table
        case 'user_signup':
            return handleUserSignup($info);  // SignUp
        case 'user_login':
            return handleUserLogin($info);  // Login
        case 'user_signout':
            return handleUserSignout($info);  // SignOut
        case 'new_folder':
            return handleNewFolder($info);  // Create Folder
        case 'soft_delete':
            return handleSoftDelete($info);  // Soft Delete
        case 'hard_delete':
            return handleHardDelete($info);  // Hard Delete
        default:
            // Handle unknown data types or errors
            $info['success'] = false;
            $info['message'] = 'Invalid data type.';
    }
} 
header('Content-Type: application/json');
echo json_encode($info);