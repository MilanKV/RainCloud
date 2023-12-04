<?php

require_once __DIR__ . '/database.php';

function get_date($date)
{
    return date("d/m/Y H:i A", strtotime($date));
}

function is_logged_in() {
    
    if(!empty($_SESSION['RAIN_USER']) && is_array($_SESSION['RAIN_USER']))
    {
        return true;
    }
    return false;
}

function get_occupied_space($user_id) 
{
    $query = "SELECT sum(file_size) AS sum FROM drive WHERE user_id = '$user_id'";
    $row = query($query);
    if($row) {
        return $row[0]['sum'];
    }
    return 0;
}

function getFolderSize($folder_id)
{
    $user_id = $_SESSION['RAIN_USER']['id'] ?? null;

    $query = "SELECT SUM(file_size) AS total_size FROM drive WHERE user_id = '$user_id' AND folder_id = '$folder_id'";
    $result = query($query);

    $total_size = 0;

    if ($result && isset($result[0]['total_size'])) {
        $total_size = (int) $result[0]['total_size'];
    }

    // Recursively calculate subfolder sizes
    $query_subfolders = "SELECT id FROM folders WHERE user_id = '$user_id' AND parent = '$folder_id'";
    $subfolders = query($query_subfolders);

    if ($subfolders && count($subfolders) > 0) {
        foreach ($subfolders as $subfolder) {
            $total_size += getFolderSize($subfolder['id']);
        }
    }

    return $total_size;
}
// Recursively soft deletes a file,folder and its contents
function recursiveSoftDelete($folder_id, $user_id) {
    // Soft delete files inside the current folder
    $query = "UPDATE drive SET soft_delete = 1 WHERE folder_id = '$folder_id' && user_id = '$user_id' && soft_delete = 0";
    query($query);

    // Soft delete the current folder
    $query = "UPDATE folders SET soft_delete = 1 WHERE id = '$folder_id' && user_id = '$user_id' && soft_delete = 0 LIMIT 1";
    query($query);

    // Get subfolders of the current folder
    $query = "SELECT id FROM folders WHERE parent = '$folder_id' && user_id = '$user_id' && soft_delete = 0";
    $subFolders = query($query);

    if(is_array($subFolders)) {
        foreach ($subFolders as $subFolder) {
            $subFolderID = $subFolder['id'];
            recursiveSoftDelete($subFolderID, $user_id);
        }
    }
}
// Recursively deletes a file,folder and its contents from the database
function recursiveHardDelete($folder_id, $user_id) {
    // First, hard delete all files inside the current folder
    $query = "DELETE FROM drive WHERE folder_id = '$folder_id' && user_id = '$user_id'";
    query($query);

    // Next, recursively hard delete subfolders and their contents
    $query = "SELECT id FROM folders WHERE parent = '$folder_id'";
    $subFolders = query($query);

    if(is_array($subFolders)) {
        foreach ($subFolders as $subFolder) {
            $subFolderID = $subFolder['id'];
            recursiveHardDelete($subFolderID, $user_id);
        }
    }

    // Finally, hard delete the current folder
    $query = "DELETE FROM folders WHERE id = '$folder_id' && user_id = '$user_id' LIMIT 1";
    query($query);
}
// Recursively retrieves the complete folder path for the given folder ID and user ID
function getFolderPathRecursive($folder_id, $user_id) {
    $folder_names = array();

    while ($folder_id != 0) {
        $query = "SELECT id, name, parent FROM folders WHERE id = '$folder_id' AND user_id = '$user_id' LIMIT 1";
        $folder_data = query($query);

        if(is_array($folder_data) && count($folder_data) > 0) {
            $folder_name = $folder_data[0]['name'];
            array_push($folder_names, $folder_name);

            $folder_id = $folder_data[0]['parent'];
        } else {
            // Folder not found, break the loop to avoid an infinite loop
            break;
        }
    }

    // Append the user folder path
    $user_folder_name = $_SESSION['RAIN_USER']['folder_name'] ?? '';
    $user_folder_path = 'storage/usersStorage/' . $user_folder_name;
    // Reverse the folder names array to get the correct order
    $folder_names = array_reverse($folder_names);
    // Join the folder names with slashes to form the folder path
    $folder_path = implode('/', $folder_names);
    // Append the user folder path and a slash if there are subfolders
    $folder_path = ($folder_path === '') ? $user_folder_path : $user_folder_path . '/' . $folder_path;

    return $folder_path;
}
// Creates the user folder if it doesn't exist
function createMissingUserFolder($user_folder_path) {
    if(!file_exists($user_folder_path)) {
        mkdir($user_folder_path, 0777, true);
    }
}