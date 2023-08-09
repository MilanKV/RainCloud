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
            
            foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {
                $file = [
                    'name' => $_FILES['files']['name'][$index],
                    'type' => $_FILES['files']['type'][$index],
                    'tmp_name' => $tmpName,
                    'error' => $_FILES['files']['error'][$index],
                    'size' => $_FILES['files']['size'][$index]
                ];
                // Check if the file already exists in the database for the specific folder
                if(checkFileExists($file['name'], $user_id, $folder_id)) {
                    continue; // Skip this file and proceed with the next one
                }
                // Check if the file upload was canceled
                if($file['error'] === UPLOAD_ERR_PARTIAL) {
                    // File upload was canceled, skip
                    continue;
                }

                $uniqueFilename = uniqid('', true) . '_' . $file['name'];
                // Append the folder path (if any) to the destination
                $destination = ($folder_path === '') ? $user_folder_path . '/' . $uniqueFilename : $user_folder_path . '/' . $folder_path . '/' . $uniqueFilename;
                
                move_uploaded_file($file['tmp_name'], $destination);
                // Check if there is enough space to save
                $occupied = $info['space_occupied'];
                $space_total = $info['space_total'] * (1024 * 1024 * 1024); // GB

                if($occupied + $file['size'] <= $space_total) {
                    // Save to database
                    $file_name = $file['name'];
                    $file_size = filesize($destination);
                    $file_type = $file['type'];
                    $file_path = $destination;
                    $user_id = $_SESSION['RAIN_USER']['id'] ?? 0;
                    $folder_id = $_POST['folder_id'] ?? 0;
                    $date_created = date("Y-m-d H:i:s");
                    $date_updated = date("Y-m-d H:i:s");

                    $query = "INSERT INTO drive 
                    (file_name, file_size, file_type, file_path, user_id, folder_id, date_created, date_updated) 
                    VALUES ('$file_name', '$file_size', '$file_type', '$file_path', '$user_id', '$folder_id', '$date_created', '$date_updated')";

                    query($query);

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
function handleGetFiles($info) {
    $selectedPage = $_POST['selected_page'] ?? 'home';
    $user_id = $_SESSION['RAIN_USER']['id'] ?? null;
    $folder_id = $_POST['folder_id'] ?? 0;

    // Breadcrumbs
    $has_parent = true;
    $num = 0;
    $myfolder_id = $folder_id;
    while($has_parent && $num < 100){
        
        $query = "SELECT * FROM folders WHERE id = '$myfolder_id' LIMIT 1";
        $row = query($query);
        if($row) {

            $info['breadcrumbs'][] = $row[0];
            if($row[0]['parent'] == 0) {
                $has_parent = false;
            } else {
                $myfolder_id = $row[0]['parent'];
            }
        }
        $num++;
    }

    if($selectedPage === 'home') {
        $query_folder = "SELECT * FROM folders WHERE user_id = '$user_id' && parent = '$folder_id' && soft_delete = 0 ORDER BY id DESC LIMIT 10";
        $query = "SELECT * FROM drive WHERE user_id = '$user_id' && folder_id = '$folder_id' && soft_delete = 0 ORDER BY id DESC LIMIT 10";

    } elseif($selectedPage === 'deleted') {
        $query_folder = "SELECT * FROM folders WHERE user_id = '$user_id' && parent = '$folder_id' && soft_delete = 1 ORDER BY id DESC LIMIT 10";
        $query = "SELECT * FROM drive WHERE user_id = '$user_id' && folder_id = '$folder_id' && soft_delete = 1 ORDER BY id DESC LIMIT 10";
    } else {
        $query_folder = "SELECT * FROM folders WHERE user_id = '$user_id' && parent = '$folder_id' ORDER BY id DESC LIMIT 10";
        $query = "SELECT * FROM drive WHERE user_id = '$user_id' && folder_id = '$folder_id' ORDER BY id DESC LIMIT 10";
    }

    $rows_folder = query($query_folder);
    $rows = query($query);
    if(!is_bool($rows) && !empty($rows_folder)) {
        
        $rows = array_merge($rows_folder, (array)$rows);
    } elseif(is_bool($rows)) {
        $rows = $rows_folder;
    }

    if(!empty($rows)) {
        foreach ($rows as &$row) {
            if(empty($row['file_type'])) {
                
                $row['file_type'] = 'folder';
                $row['file_size'] = 0;
                $row['file_name'] = $row['name'];
                $row['date_created'] = $row['date_created'];
                $row['date_updated'] = $row['date_updated'];

                // Get the folder size if it's a folder
                if($row['file_type'] == 'folder') {
                    $folder_id = $row['id'];
                    $row['file_size'] = getFolderSize($folder_id);
                }
            }

            $part = explode(".", $row['file_name']);
            $ext = strtoLower(end($part));
            $row['icon'] = get_icon($row['file_type'], $ext);
            $row['date_updated'] = get_date($row['date_updated']);
            $row['date_created'] = get_date($row['date_created']);
        }
        
        $info['rows'] = $rows;
        $info['success'] = true;
    }
    echo json_encode($info);
}

function getFolderPath($folder_id, $user_id) {
    $folder_path = '';
    
    if($folder_id !== 0) {
        $query = "SELECT id, name, parent FROM folders WHERE id = '$folder_id' AND user_id = '$user_id' LIMIT 1";
        $folder_data = query($query);
        if(is_array($folder_data) && count($folder_data) > 0) {
            $folder_names = array();
            while ($folder_data[0]['parent'] != 0) {
                array_unshift($folder_names, $folder_data[0]['name']);
                $folder_data = query("SELECT id, name, parent FROM folders WHERE id = '{$folder_data[0]['parent']}' AND user_id = '$user_id' LIMIT 1");
            }
            array_unshift($folder_names, $folder_data[0]['name']);
            $folder_path = implode('/', $folder_names);
        }
    }
    return $folder_path;
}