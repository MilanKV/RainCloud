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