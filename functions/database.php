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

function fetchUserId($email)
{
    $email = mysqli_real_escape_string(db_connect(), $email);

    $user_query = "SELECT id FROM users WHERE email = '$email'";
    $user_result = query($user_query);

    if($user_result && count($user_result) > 0) {
        $user_row = $user_result[0];
        
        return $user_row['id'];
    }

    return null;
}