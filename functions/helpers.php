<?php

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/file_functions.php';

function get_date($date){
    return date("d/m/Y H:i A", strtotime($date));
}

function is_logged_in() {
    
    if(!empty($_SESSION['RAIN_USER']) && is_array($_SESSION['RAIN_USER']))
    {
        return true;
    }
    return false;
}

function get_occupied_space($user_id) {
    $query = "SELECT sum(file_size) AS sum FROM drive WHERE user_id = '$user_id'";
    $row = query($query);
    if($row) {
        return $row[0]['sum'];
    }
    return 0;
}

function getFolderSize($folder_id) {
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
// Function to retrieve the folder path based on folder ID and user ID
function getFolderPath($folder_id, $user_id) {
    $folder_path = '';
    
    // Check if the folder ID is not the root folder
    if($folder_id !== 0) {
        // Query to retrieve folder information
        $query = "SELECT id, name, parent FROM folders WHERE id = '$folder_id' AND user_id = '$user_id' LIMIT 1";
        $folder_data = query($query);
        // Build folder path by traversing parent folders
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
// Function to save file information(FileUpload) to the database
function saveFileToDatabase($file, $destination, $user_id, $folder_id) {
    $file_name = $file['name'];
    $file_size = filesize($destination);
    $file_type = $file['type'];
    $date_created = date("Y-m-d H:i:s");
    $date_updated = date("Y-m-d H:i:s");

    $query = "INSERT INTO drive 
        (file_name, file_size, file_type, file_path, user_id, folder_id, date_created, date_updated) 
        VALUES ('$file_name', '$file_size', '$file_type', '$destination', '$user_id', '$folder_id', '$date_created', '$date_updated')";

    query($query);
}
// Function to retrieve breadcrumbs for a folder
function getBreadcrumbs($folder_id) {
    $breadcrumbs = [];
    $has_parent = true;
    $num = 0;
    $myfolder_id = $folder_id;

    // Retrieve breadcrumbs by traversing parent folders
    while ($has_parent && $num < 100) {
        $query = "SELECT * FROM folders WHERE id = '$myfolder_id' LIMIT 1";
        $row = query($query);

        if ($row) {
            $breadcrumbs[] = $row[0];
            if ($row[0]['parent'] == 0) {
                $has_parent = false;
            } else {
                $myfolder_id = $row[0]['parent'];
            }
        }
        $num++;
    }

    return $breadcrumbs;
}
// Function to retrieve file details and update the row
function getFileDetails(&$row) {
    // Set default values for folder entries
    if (empty($row['file_type'])) {
        $row['file_type'] = 'folder';
        $row['file_size'] = 0;
        $row['file_name'] = $row['name'];
        $row['date_created'] = $row['date_created'];
        $row['date_updated'] = $row['date_updated'];

        // Get the folder size if it's a folder
        if ($row['file_type'] == 'folder') {
            $folder_id = $row['id'];
            $row['file_size'] = getFolderSize($folder_id);
        }
    }
    // Extract file extension and determine icon
    $part = explode(".", $row['file_name']);
    $ext = strtolower(end($part));
    $row['icon'] = get_icon($row['file_type'], $ext);
    $row['date_updated'] = get_date($row['date_updated']);
    $row['date_created'] = get_date($row['date_created']);
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