<?php

require_once __DIR__ . '/../config.php';

function db_connect() 
{
    $HOST = 'localhost';
    $USER = 'root';
    $PASS = '';
    $DBNAME = 'raincloud_db';

    $conn = new mysqli($HOST, $USER, $PASS, $DBNAME);

    if($conn->connect_error) {
        die("Connection Faild:" . $conn->connect_error);
    }

    return $conn;
}

function query($query) 
{
    $conn = db_connect();
    
    $result = mysqli_query($conn, $query);

    if($result) {
        if (!is_bool($result) && mysqli_num_rows($result) > 0) {
            $res = [];
            
            while ($row = mysqli_fetch_assoc($result)) {
                $res[] = $row;
            }
            return $res;
        }
    }
    return false;
}
// Function to check if a file already exists in the database by its name
function checkFileExists($filename, $folder_id)
{
    $filename = addslashes($filename);
    $folder_id = intval($folder_id);

    $query = "SELECT id FROM drive WHERE file_name = '$filename' AND folder_id = '$folder_id' LIMIT 1";
    $row = query($query);
    return !empty($row);
}

// Function to check if a folder already exists in the database by its name for a specific user
function checkFolderExists($folder_name, $user_id, $parent_folder_id)
{
    $folder_name = addslashes($folder_name);
    $query = "SELECT id FROM folders WHERE name = '$folder_name' AND user_id = '$user_id' AND parent = '$parent_folder_id' LIMIT 1";
    $row = query($query);
    return !empty($row);
}