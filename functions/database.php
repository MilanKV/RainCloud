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
function executeQuery($conn, $query, $params = array()) {
    if (!empty($params)) {
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $paramTypes = '';

            foreach ($params as $param) {
                if (is_int($param)) {
                    $paramTypes .= 'i'; // integer
                } elseif (is_double($param) || is_float($param)) {
                    $paramTypes .= 'd'; // double/float
                } else {
                    $paramTypes .= 's'; // string
                }
            }

            $stmt->bind_param($paramTypes, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        } else {
            return false;
        }
    } else {
        $result = mysqli_query($conn, $query);
        return $result;
    }
}
function query($query, $params = array()) {
    $conn = db_connect();
    
    $result = executeQuery($conn, $query, $params);
    
    if ($result === false) {
        die("Database query failed: " . mysqli_error($conn));
    } elseif ($result !== true && mysqli_num_rows($result) > 0) {
        $res = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $res[] = $row;
        }
        return $res;
    } elseif ($result === true) {
        return true; // If there's no result, return true (for INSERT/UPDATE/DELETE queries)
    }
    
    return false;
}
// Function to check if a file already exists in the database by its name
function checkFileExists($file_name, $user_id, $folder_id)
{
    $file_name = addslashes($file_name);
    $user_id = intval($user_id);
    $folder_id = intval($folder_id);

    // Check if a file with the same name already exists for the same user and folder
    $query = "SELECT id FROM drive WHERE file_name = '$file_name' AND user_id = '$user_id' AND folder_id = '$folder_id' AND soft_delete = 0 LIMIT 1";
    $row = query($query);
    return !empty($row);
}

// Function to check if a folder already exists in the database by its name for a specific user
function checkFolderExists($folder_name, $user_id, $parent_folder_id)
{
    $folder_name = addslashes($folder_name);
    $user_id = intval($user_id);
    $parent_folder_id = intval($parent_folder_id);

    // Check if a folder with the same name already exists for the same user and parent folder
    $query = "SELECT id FROM folders WHERE name = '$folder_name' AND user_id = '$user_id' AND parent = '$parent_folder_id' AND soft_delete = 0 LIMIT 1";
    $row = query($query);
    return !empty($row);
}