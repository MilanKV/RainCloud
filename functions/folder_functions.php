<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/database.php';

function handleNewFolder($info) {
    $name = addslashes($_POST['name']);
    $parent = $_POST['folder_id'] ?? 0;
    $user_id = $_SESSION['RAIN_USER']['id'] ?? 0;
    
    if(checkFolderExists($name, $user_id, $parent)) {
        $info['success'] = false;
        $info['message'] = "A folder with the same name already exists.";
    } else {
        // Save to database
        $user_id = $_SESSION['RAIN_USER']['id'] ?? 0;
        $parent = $_POST['folder_id'] ?? 0;
        $date_created = date("Y-m-d H:i:s");
        $date_updated = date("Y-m-d H:i:s");

        $query = "INSERT INTO folders 
        (name, user_id, parent, date_created, date_updated) VALUES ('$name', '$user_id', '$parent', '$date_created', '$date_updated')";

        query($query);
        // Get the newly created folder's ID
        $new_folder_id = mysqli_insert_id(db_connect());

        $info['success'] = true;
        $info['message'] = "Folder created successfully.";

        // Create the folder in the local storage
        $user_folder_name = $_SESSION['RAIN_USER']['folder_name'] ?? '';
        $user_folder_path = 'storage/usersStorage/' . $user_folder_name;

        if($parent !== 0) {
            // Get the parent folder's path
            $parent_folder_query = "SELECT * FROM folders WHERE id = '$parent' LIMIT 1";
            $parent_folder_data = query($parent_folder_query);

            if(is_array($parent_folder_data) && count($parent_folder_data) > 0) {
                $parent_folder_name = $parent_folder_data[0]['name'];
                $parent_folder_id = $parent_folder_data[0]['id'];
                // Get the parent folder's path
                $parent_folder_path = getFolderPathRecursive($parent_folder_id, $user_id, $user_folder_path);

                if(!file_exists($parent_folder_path)) {
                    mkdir($parent_folder_path);
                }

                $user_folder_path = $parent_folder_path;
            }
        }
        // Create the new folder in the local storage
        $new_folder_path = $user_folder_path . '/' . $name;

        if(!file_exists($new_folder_path)) {
            mkdir($new_folder_path);
        }
    } 
    echo json_encode($info);
}

function handleSoftDelete($info) {
    // Soft delete
    if(isset($_POST['id'], $_POST['file_type'])) {
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
    }
    echo json_encode($info);
}

function handleHardDelete($info) {
    if(isset($_POST['id'], $_POST['file_type'])) {
        $ids = $_POST['id'];
        $file_types = $_POST['file_type'];
        $user_id = $_SESSION['RAIN_USER']['id'];
        $successCount = 0;

        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $file_type = $file_types[$i];

            if($file_type == 'folder') {
                recursiveHardDelete($id, $user_id);
            } else {
                // For files, perform the hard delete
                $query = "DELETE FROM drive WHERE id = '$id' && user_id = '$user_id' && soft_delete = 1 LIMIT 1";
                $queryResult = query($query);

                if($queryResult) {
                    $successCount++;
                }
            }            
        }
        if($successCount == count($ids)) {
            $info['success'] = true;
            $info['message'] = "File/Folder permanently deleted.";
        } else {
            $info['success'] = false;
            $info['message'] = "Missing 'id' or 'file_type' in the request.";
        }
    } else {
        $info['success'] = false;
        $info['message'] = "Missing 'id' or 'file_type' in the request.";
    }
    echo json_encode($info);
}
