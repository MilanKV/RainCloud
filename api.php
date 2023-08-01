<?php

session_start();
require_once __DIR__ . '/functions/database.php';
require_once __DIR__ . '/functions/helpers.php';
require_once __DIR__ . '/functions/file_icons.php';

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

$without_login = ['user_signup', 'user_login'];
if(!$info['LOGGED_IN'] && (!in_array($info['data_type'], $without_login)))
{
    $response = json_encode($info);
    echo $response;
    die;
}

if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['data_type'])) {

    $info['data_type'] = $_POST['data_type'];

    if($_POST['data_type'] == "upload_files") 
    {
        $folder = 'uploads/';
        if(!file_exists($folder)) {

            mkdir($folder, 0777, true);
            file_put_contents($folder. ".HTACCESS", "Options -Indexes");
        }

        foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {
            
            $file = [
                'name' => $_FILES['files']['name'][$index],
                'type' => $_FILES['files']['type'][$index],
                'tmp_name' => $tmpName,
                'error' => $_FILES['files']['error'][$index],
                'size' => $_FILES['files']['size'][$index]
            ];

            // Check if the file already exists in the database for the specific folder
            $folder_id = $_POST['folder_id'] ?? 0;
            if (checkFileExists($file['name'], $folder_id)) {
                continue; // Skip this file and proceed with the next one
            }

            // Check if the file upload was canceled
            if ($file['error'] === UPLOAD_ERR_PARTIAL) {
                // The file upload was canceled, so skip processing this file
                continue;
            }

            $uniqueFilename = uniqid('', true) . '_' . $file['name'];
            $destination = $folder . $uniqueFilename;

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
    } else 
    if($_POST['data_type'] == "get_files") 
    {
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

        if ($selectedPage === 'home') {
            $query_folder = "SELECT * FROM folders WHERE user_id = '$user_id' && parent = '$folder_id' && soft_delete = 0 ORDER BY id DESC LIMIT 10";
            $query = "SELECT * FROM drive WHERE user_id = '$user_id' && folder_id = '$folder_id' && soft_delete = 0 ORDER BY id DESC LIMIT 10";

        } elseif ($selectedPage === 'deleted') {
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
        } elseif (is_bool($rows)) {
            $rows = $rows_folder;
        }

        if(!empty($rows))
        {
            foreach ($rows as &$row) 
            {
                if(empty($row['file_type'])) {
                    
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

                $part = explode(".", $row['file_name']);
                $ext = strtoLower(end($part));
                $row['icon'] = get_icon($row['file_type'], $ext);
                $row['date_updated'] = get_date($row['date_updated']);
                $row['date_created'] = get_date($row['date_created']);
            }
            
            $info['rows'] = $rows;
            $info['success'] = true;
        }
    } else
    if($_POST['data_type'] == "user_signup") 
    {
        // Save to database
        $email = addslashes($_POST['email']);
        $name = addslashes($_POST['name']);
        $password = addslashes($_POST['password']);
        $password_confirmation = addslashes($_POST['password_confirmation']);
        $date_created = date("Y-m-d H:i:s");
        $date_updated = date("Y-m-d H:i:s");

        // Validate data
        $errors = [];

        if (empty($name) || !preg_match("/^[a-zA-Z ]+$/", $name)) 
        {
            $errors['name'] = "Invalid name";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email address";
        } elseif (query("SELECT id FROM users WHERE email = '$email' LIMIT 1")) {
            $errors['email'] = "Email already exists";
        }

        if (empty($password)) {
            $errors['password'] = "Password is required";
        } elseif (strlen($password) < 8) {
            $errors['password'] = "Password must be at least 8 characters";
        }

        if ($password !== $password_confirmation) {
            $errors['password_confirmation'] = "Passwords do not match";
        }

        if (empty($name) && empty($email) && empty($password) && empty($password_confirmation)) {
            $errors['empty_inputs'] = "Please fill in all the fields";
        }

        if(empty($errors))
        {
            $password = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO users 
            (name, email, password, date_created, date_updated) 
            VALUES ('$name', '$email', '$password', '$date_created', '$date_updated')";
    
            query($query);
    
            $info['success'] = true;  
        }
        $info['errors'] = $errors;

    } else
    if($_POST['data_type'] == "user_login") 
    {
        // Save to database
        $email = addslashes($_POST['email']);
        $password = addslashes($_POST['password']);

        // Validate data
        $errors = [];

        if (empty($email)) {
            $errors['email'] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email address";
        }
    
        if (empty($password)) {
            $errors['password'] = "Password is required";
        }
    
        if (empty($email) && empty($password)) {
            $errors['empty_inputs'] = "Please fill in all the fields";
        }    

        if(empty($errors))
        {
            $row = query("SELECT * FROM users WHERE email = '$email' LIMIT 1");
    
            if(!empty($row))
            {
                $row = $row[0];
                if(password_verify($password, $row['password']))
                {
                    $info['success'] = true;
                    $_SESSION['RAIN_USER'] = $row;
                } else {
                    $errors['login_failed'] = "Invalid email or password";
                }
            } else {
                $errors['login_failed'] = "Invalid email or password";
            }
        }
        $info['errors'] = $errors;
    } else
    if($_POST['data_type'] == "user_signout")  
    {
        if(isset($_SESSION['RAIN_USER']))
            unset($_SESSION['RAIN_USER']);
        
        $info['success'] = true;
    } else
    if($_POST['data_type'] == "new_folder")
    {
        $name = addslashes($_POST['name']);
        $parent = $_POST['folder_id'] ?? 0;
        $user_id = $_SESSION['RAIN_USER']['id'] ?? 0;
        
        if (checkFolderExists($name, $user_id, $parent)) {
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

            $info['success'] = true;
            $info['message'] = "Folder created successfully.";
        }
        
    } else 
    if($_POST['data_type'] == "soft_delete") {
        // Soft delete
        if(isset($_POST['id'], $_POST['file_type'])) {
            $id = $_POST['id'];
            $file_type = $_POST['file_type'];
            $user_id = $_SESSION['RAIN_USER']['id'];

            if($file_type == 'folder') {
                recursiveSoftDelete($id, $user_id);
            } else {
                $query = "UPDATE drive SET soft_delete = 1 WHERE id = '$id' && user_id = '$user_id' LIMIT 1";
                $queryResult = query($query, [$id, $user_id]);

                if ($queryResult) {
                    $info['success'] = true;
                    $info['message'] = "File/Folder deleted successfully.";
                } else {
                    $info['success'] = false;
                    $info['message'] = "Failed to delete the file/folder.";
                    $info['query_error'] = $queryResult;
                }
            }
        }
    } else
    if ($_POST['data_type'] == "hard_delete") {
        if (isset($_POST['id'], $_POST['file_type'])) {
            $ids = $_POST['id'];
            $file_types = $_POST['file_type'];
            $user_id = $_SESSION['RAIN_USER']['id'];
            $successCount = 0;

            for ($i = 0; $i < count($ids); $i++) {
                $id = $ids[$i];
                $file_type = $file_types[$i];

                if ($file_type == 'folder') {
                    recursiveHardDelete($id, $user_id);
                } else {
                    // For files, perform the hard delete
                    $query = "DELETE FROM drive WHERE id = '$id' && user_id = '$user_id' && soft_delete = 1 LIMIT 1";
                    $queryResult = query($query);

                    if ($queryResult) {
                        $successCount++;
                    }
                }            
            }
            if ($successCount == count($ids)) {
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
    }
} 
// Soft delete
function recursiveSoftDelete($folder_id, $user_id) {
    // Soft delete files inside the current folder
    $query = "UPDATE drive SET soft_delete = 1 WHERE folder_id = '$folder_id' && user_id = '$user_id'";
    query($query);

    // Soft delete the current folder
    $query = "UPDATE folders SET soft_delete = 1 WHERE id = '$folder_id' && user_id = '$user_id' LIMIT 1";
    query($query);

    // Get subfolders of the current folder
    $query = "SELECT id FROM folders WHERE parent = '$folder_id'";
    $subFolders = query($query);

    if (is_array($subFolders)) {
        foreach ($subFolders as $subFolder) {
            $subFolderID = $subFolder['id'];
            recursiveSoftDelete($subFolderID, $user_id);
        }
    }
}
// Hard delete
function recursiveHardDelete($folder_id, $user_id)
{
    // First, hard delete all files inside the current folder
    $query = "DELETE FROM drive WHERE folder_id = '$folder_id' && user_id = '$user_id'";
    query($query);

    // Next, recursively hard delete subfolders and their contents
    $query = "SELECT id FROM folders WHERE parent = '$folder_id'";
    $subFolders = query($query);

    if (is_array($subFolders)) {
        foreach ($subFolders as $subFolder) {
            $subFolderID = $subFolder['id'];
            recursiveHardDelete($subFolderID, $user_id);
        }
    }

    // Finally, hard delete the current folder
    $query = "DELETE FROM folders WHERE id = '$folder_id' && user_id = '$user_id' LIMIT 1";
    query($query);
}

header('Content-Type: application/json');

$response = json_encode($info);
echo $response;