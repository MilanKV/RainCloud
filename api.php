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
];

$logged_in = $info['LOGGED_IN'];

$without_login = ['user_signup', 'user_login'];
if(!$info['LOGGED_IN'] && (!in_array($info['data_type'], $without_login)))
{
    echo json_encode($info);
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

        // Check if user is logged in and fetch the user ID
        if(isset($_SESSION['RAIN_USER']['email'])) {
            $logged_in = $_SESSION['RAIN_USER']['email'];
            
            // Fetch the user ID, based on the logged-in user's name
            $user_id = fetchUserId($logged_in);

            if($user_id !== null) {

                foreach ($_FILES as $key => $file) {
        
                    $destination = $folder. time() . $file['name'];
                    if(file_exists($destination))
                        $destination = $folder. time() . rand(0,9999) . $file['name'];
        
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
                        $date_created = date("Y-m-d H:i:s");
                        $date_updated = date("Y-m-d H:i:s");
            
                        $query = "INSERT INTO drive 
                        (file_name, file_size, file_type, file_path, user_id, date_created, date_updated) 
                        VALUES ('$file_name', '$file_size', '$file_type', '$file_path', '$user_id', '$date_created', '$date_updated')";
            
                        query($query);
            
                        $info['success'] = true;
                    } else {
                        $info['success'] = false;
                        $info['message'] = "You don't have enough space";
                    }
                }
            } else {
                $info['success'] = false;
                $info['message'] = "User not found";
            }
        }
    } else 
    if($_POST['data_type'] == "get_files") 
    {
        $user_id = $_SESSION['RAIN_USER']['id'] ?? null;
        
        $query = "SELECT * FROM drive WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 10";
        $rows = query($query);
        if($rows)
        {
            foreach ($rows as &$row) 
            {
                $part = explode(".", $row['file_name']);
                $ext = strtoLower(end($part));
                $row['icon'] = get_icon($row['file_type'], $ext);
                $file_size = round($row['file_size'] / (1024 * 1024), 2);
                $row['file_size'] = ($file_size >= 1) ? number_format($file_size, 2) . "MB" : number_format($row['file_size'] / 1024, 2) . "KB";
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
    }
} 

echo json_encode($info);