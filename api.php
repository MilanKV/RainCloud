<?php

session_start();
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';

$info = [
    'success' => false,
];

if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['data_type'])) {

    $info['data_type'] = $_POST['data_type'];

    if($_POST['data_type'] == "upload_files") 
    {
        $folder = 'uploads/';
        if(!file_exists($folder)) {

            mkdir($folder, 0777, true);
            file_put_contents($folder. ".HTACCESS", "Options -Indexes");
        }

        foreach ($_FILES as $key => $file) {

            $destination = $folder. time() . $file['name'];
            if(file_exists($destination))
                $destination = $folder. time() . rand(0,9999) . $file['name'];

            move_uploaded_file($file['tmp_name'], $destination);

            // Save to database
            $file_name = $file['name'];
            $file_size = filesize($destination);
            $file_type = $file['type'];
            $file_path = $destination;
            $user_id = 0;
            $date_created = date("Y-m-d H:i:s");
            $date_updated = date("Y-m-d H:i:s");

            $query = "insert into drive 
            (file_name, file_size, file_type, file_path, user_id, date_created, date_updated) 
            values ('$file_name', '$file_size', '$file_type', '$file_path', '$user_id', '$date_created', '$date_updated')";

            query($query);

            $info['success'] = true;
        }
    } else {
        if($_POST['data_type'] == "get_files") 
        {
            $query = "select * from drive order by id desc limit 25";
            $rows = query($query);
            if($rows)
            {
                foreach ($rows as $key => $row) 
                {
                    $rows[$key]['file_size'] = round($row['file_size'] / (1024 * 1024)) . "MB";
                    if($rows[$key]['file_size'] == "0MB") 
                    {
                        $rows[$key]['file_size'] = round($row['file_size'] / (1024)) . "kB";
                    }
                    $rows[$key]['date_updated'] = get_date($row['date_updated']);
                }
                
                $info['rows'] = $rows;
                $info['success'] = true;
            }
        }
    }
} 

echo json_encode($info);