<?php

function get_date($date)
{
    return date("d/m/Y h:i A", strtotime($date));
}

function is_logged_in() {
    
    if(!empty($_SESSION['RAIN_USER']) && is_array($_SESSION['RAIN_USER']))
    {
        return true;
    }
    return false;
}