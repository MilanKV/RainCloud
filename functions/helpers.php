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