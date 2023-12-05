<?php

require_once __DIR__ . '/database.php';

function handleUserSignup($info) {
    // Retrieve POST data
    $name = trim($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);;
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];

    $errors = [];
    // Validate name
    if(empty($name) || !preg_match("/^[a-zA-Z ]+$/", $name)) {
        $errors['name'] = "Invalid name";
    }
    // Validate email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email address";
    } else {
        // Check if email already exists
        $existingUser = query("SELECT id FROM users WHERE email = '$email' LIMIT 1");
        if($existingUser) {
            $errors['email'] = "Email already exists";
        }
    }
    // Validate password
    if(empty($password)) {
        $errors['password'] = "Password is required";
    } elseif(strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters";
    }
    // Validate password confirmation
    if($password !== $password_confirmation) {
        $errors['password_confirmation'] = "Passwords do not match";
    }
    // Check for empty inputs
    if(empty($name) && empty($email) && empty($password) && empty($password_confirmation)) {
        $errors['empty_inputs'] = "Please fill in all the fields";
    }

    if(empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $date = date("Y-m-d H:i:s");
        
        $query = "INSERT INTO users 
        (name, email, password, date_created, date_updated) 
        VALUES ('$name', '$email', '$passwordHash', '$date', '$date')";
        
        query($query);
        
        // Get the user ID from the database after insertion
        $user_data = query("SELECT id FROM users WHERE email = '$email' LIMIT 1");
        $user_id = $user_data[0]['id'];
        // Create a new folder for the user with their id and name
        $user_folder_name = $user_id . '_' . preg_replace("/[^A-Za-z0-9]/", '_', $name);
        $folder_path = 'storage/usersStorage/' . $user_folder_name;

        if(!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        // Store user data in session
        $_SESSION['RAIN_USER'] = [
            'id' => $user_id,
            'name' => $name,
            'email' => $email,
            'folder_name' => $user_folder_name,
            'LOGGED_IN' => true
        ];

        $info['success'] = true;
    } else {
        $info['success'] = false;
        $info['errors'] = $errors;
    }
    echo json_encode($info);
}
function handleUserLogin($info) {
    // Save to database
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $errors = [];
    // Validate email
    if(empty($email)) {
        $errors['email'] = "Email is required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email address";
    }
    // Validate password
    if(empty($password)) {
        $errors['password'] = "Password is required";
    }
    // Check for empty fields
    if(empty($email) && empty($password)) {
        $errors['empty_inputs'] = "Please fill in all the fields";
    }    
    // If no validation errors, attempt login
    if(empty($errors)) {
        $row = query("SELECT * FROM users WHERE email = '$email' LIMIT 1");

        if(!empty($row)) {
            $row = $row[0];
            if(password_verify($password, $row['password'])) {
                $_SESSION['RAIN_USER'] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'folder_name' => $row['id'] . '_' . preg_replace("/[^A-Za-z0-9]/", '_', $row['name']),
                    'LOGGED_IN' => true,
                ]; 
                $info['success'] = true;                          
            } else {
                $errors['login_failed'] = "Invalid email or password";
            }
        } else {
            $errors['login_failed'] = "Invalid email or password";
        }
    }
    if (!empty($errors)) {
        $info['success'] = false;
        $info['errors'] = $errors;
    }
    echo json_encode($info);
}
function handleUserSignout($info) {
    if (isset($_SESSION['RAIN_USER'])) {
        unset($_SESSION['RAIN_USER']);
        $info['success'] = true;
    } else {
        $info['success'] = false;
    }
}