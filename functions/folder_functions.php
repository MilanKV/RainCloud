<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/database.php';

function handleNewFolder($info) {
    $name = addslashes($_POST['name'] ?? '');
    $parent = $_POST['folder_id'] ?? 0;
    $user_id = $_SESSION['RAIN_USER']['id'] ?? 0;
    
    if(checkFolderExists($name, $user_id, $parent)) {
        $info['success'] = false;
        $info['message'] = "A folder with the same name already exists.";
    } else {
        $date_created = $date_updated = date("Y-m-d H:i:s");

        // Save to database
        $query = "INSERT INTO folders 
        (name, user_id, parent, date_created, date_updated) 
        VALUES ('$name', '$user_id', '$parent', '$date_created', '$date_updated')";
        query($query);

        // Get the newly created folder's ID
        $new_folder_id = mysqli_insert_id(db_connect());

        $info['success'] = true;
        $info['message'] = "Folder created successfully.";

        // Create the folder in the local storage
        $user_folder_path = getUserFolderPath($user_id, $parent);
        // Create the new folder in the local storage
        $new_folder_path = $user_folder_path . '/' . $name;
        createFolder($new_folder_path);  
    } 
    echo json_encode($info);
}

function handleSoftDelete($info) {
    $ids = $_POST['id'];
    $file_types = $_POST['file_type'];
    $user_id = $_SESSION['RAIN_USER']['id'];

    for ($i = 0; $i < count($ids); $i++) {
        $id = $ids[$i];
        $file_type = $file_types[$i];

        if($file_type == 'folder') {
            recursiveSoftDelete($id, $user_id);
        } else {
            $query = "UPDATE drive SET soft_delete = 1 WHERE id = '$id' && user_id = '$user_id' && soft_delete = 0 LIMIT 1";
            $queryResult = query($query);

            if(!$queryResult) {
                $info['success'] = false;
                $info['message'] = "Failed to soft delete the selected items.";
            }
        }
    }
    $info['success'] = true;
    $info['message'] = "File/Folder soft deleted successfully.";

    echo json_encode($info);
}

function handleHardDelete($info) {
    $ids = $_POST['id'] ?? [];
    $file_types = $_POST['file_type'] ?? [];
    $user_id = $_SESSION['RAIN_USER']['id'];
    $successCount = 0;

    foreach (array_map(null, $ids, $file_types) as [$id, $file_type]) {
        if ($file_type === 'folder') {
            recursiveHardDelete($id, $user_id);
        } elseif ($file_type === 'file') {
            // For files, perform the hard delete
            $query = "DELETE FROM drive WHERE id = '$id' AND user_id = '$user_id' AND soft_delete = 1 LIMIT 1";
            $queryResult = query($query);

            if ($queryResult) {
                $successCount++;
            }
        }
    }

    if ($successCount === count($ids)) {
        $info['success'] = true;
        $info['message'] = "File/Folder permanently deleted.";
    } else {
        $info['success'] = false;
        $info['message'] = "Missing 'id' or 'file_type' in the request.";
    }

    echo json_encode($info);
}

function getUserFolderPath($user_id, $parent) {
    $user_folder_name = $_SESSION['RAIN_USER']['folder_name'] ?? '';
    $user_folder_path = 'storage/usersStorage/' . $user_folder_name;

    if ($parent !== 0) {
        $parent_folder_path = getFolderPathRecursive($parent, $user_id);
        createFolder($parent_folder_path);
        $user_folder_path = $parent_folder_path;
    }

    return $user_folder_path;
}

function createFolder($path) {
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
}