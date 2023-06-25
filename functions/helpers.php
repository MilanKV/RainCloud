<?php

function get_date($date)
{
    return date("d/m/Y h:i A", strtotime($date));
}