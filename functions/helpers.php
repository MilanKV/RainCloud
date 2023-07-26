<?php

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