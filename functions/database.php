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

function query($query, $params = array()) 
{
    $conn = db_connect();
    if (!empty($params)) {
        // Use prepared statement with parameter binding
        $stmt = $conn->prepare($query);

        // Check if the prepared statement is valid
        if ($stmt) {
            // Bind parameters to the prepared statement
            if (count($params) > 0) {
                $paramTypes = '';
                $bindParams = array();

                foreach ($params as &$param) {
                    if (is_int($param)) {
                        $paramTypes .= 'i'; // integer
                    } elseif (is_double($param) || is_float($param)) {
                        $paramTypes .= 'd'; // double/float
                    } elseif (is_string($param)) {
                        $paramTypes .= 's'; // string
                    } else {
                        $paramTypes .= 's'; // default to string if the type cannot be determined
                    }
                    $bindParams[] = $param;
                }

                array_unshift($bindParams, $paramTypes);
                call_user_func_array(array($stmt, 'bind_param'), $bindParams);
            }

            // Execute the prepared statement
            $stmt->execute();

            // Get the result (if any)
            $result = $stmt->get_result();

            // If the result is not empty, fetch the rows
            if ($result && mysqli_num_rows($result) > 0) {
                $res = array();
                while ($row = $result->fetch_assoc()) {
                    $res[] = $row;
                }
                $stmt->close();
                return $res;
            }

            $stmt->close();
            return true; // If there's no result, return true (for INSERT/UPDATE/DELETE queries)
        } else {
            // If the prepared statement is invalid, return false
            return false;
        }
    } else {
        // If there are no parameters, execute the query directly
        $result = mysqli_query($conn, $query);
    
        if ($result !== true && mysqli_num_rows($result) > 0) {
            $res = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $res[] = $row;
            }
            return $res;
        }
        if ($result === false) {
            die("Database query returned false without an error: " . mysqli_error($conn));
        }
        return false;
    }
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