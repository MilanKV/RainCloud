<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/file_icons.php';
require_once __DIR__ . '/database.php';

// Handle Multiple File Uploads
function handleFileUpload($info) {
    // Check if the user is logged in
    if(!$info['LOGGED_IN']) {
        $info['success'] = false;
        $info['message'] = "You must be logged in to upload files.";
    } else {
        // Get user ID and folder name from session
        $user_id = $_SESSION['RAIN_USER']['id'] ?? 0;
        $user_folder_name = $_SESSION['RAIN_USER']['folder_name'] ?? '';
        $user_folder_path = 'storage/usersStorage/' . $user_folder_name;

        // Check if the user's folder exists
        if(!file_exists($user_folder_path)) {
            createMissingUserFolder($user_folder_path);
            $info['success'] = false;
            $info['message'] = "User folder not found. Please create the user folder first.";
        } else {
            // Get the folder path based on the folder_id (if provided)
            $folder_id = $_POST['folder_id'] ?? 0;
            $folder_path = getFolderPath($folder_id, $user_id);

            // Loop through each uploaded file
            foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {
                // Extract file details
                $file = [
                    'name' => $_FILES['files']['name'][$index],
                    'type' => $_FILES['files']['type'][$index],
                    'tmp_name' => $tmpName,
                    'error' => $_FILES['files']['error'][$index],
                    'size' => $_FILES['files']['size'][$index]
                ];

                // Check if the file already exists or if the upload was canceled
                if(checkFileExists($file['name'], $user_id, $folder_id) || $file['error'] === UPLOAD_ERR_PARTIAL) {
                    continue;
                }

                // Generate a unique filename and set the destination path
                $uniqueFilename = uniqid('', true) . '_' . $file['name'];
                $destination = ($folder_path === '') ? $user_folder_path . '/' . $uniqueFilename : $user_folder_path . '/' . $folder_path . '/' . $uniqueFilename;

                // Move the uploaded file to its destination
                move_uploaded_file($file['tmp_name'], $destination);

                // Check if there is enough space to save the file
                $occupied = $info['space_occupied'];
                $space_total = $info['space_total'] * (1024 * 1024 * 1024); // GB

                if($occupied + $file['size'] <= $space_total) {
                    // Save file details to the database
                    saveFileToDatabase($file, $destination, $user_id, $folder_id);
                    $info['success'] = true;
                } else {
                    $info['success'] = false;
                    $info['message'] = "You don't have enough space";
                }
            }
        }
    }

    echo json_encode($info);
}
// File Handling
function handleGetFiles($info) {
    // Retrieve information from POST data and session
    $selectedPage = $_POST['selected_page'] ?? 'home';
    $user_id = $_SESSION['RAIN_USER']['id'] ?? null;
    $folder_id = $_POST['folder_id'] ?? 0;

    // Breadcrumbs
    $info['breadcrumbs'] = getBreadcrumbs($folder_id);

    // Define conditions based on the selected page for soft deletion
    $soft_delete_condition = ($selectedPage === 'deleted') ? "&& soft_delete = 1" : "&& soft_delete = 0";

    // Query to retrieve folders within the specified parent folder
    $query_folder = "SELECT * FROM folders WHERE user_id = '$user_id' && parent = '$folder_id' $soft_delete_condition ORDER BY id DESC LIMIT 10";
    // Query to retrieve files within the specified folder
    $query = "SELECT * FROM drive WHERE user_id = '$user_id' && folder_id = '$folder_id' $soft_delete_condition ORDER BY id DESC LIMIT 10";

    $rows_folder = query($query_folder);
    $rows = query($query);

    // Merge folder and file results if both are not empty
    if(!is_bool($rows) && !empty($rows_folder)) {
        
        $rows = array_merge($rows_folder, (array)$rows);
    } elseif(is_bool($rows)) {
        $rows = $rows_folder;
    }

    // Process file details and add to the result
    if(!empty($rows)) {
        foreach ($rows as &$row) {
            getFileDetails($row);
        }
        
        $info['rows'] = $rows;
        $info['success'] = true;
    }
    echo json_encode($info);
}