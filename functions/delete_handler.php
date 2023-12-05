<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/database.php';

function handleSoftDelete($info) {
    $ids = $_POST['id'];
    $file_types = $_POST['file_type'];
    $user_id = $_SESSION['RAIN_USER']['id'];

    for ($i = 0; $i < count($ids); $i++) {
        $id = $ids[$i];
        $file_type = $file_types[$i];

        if($file_type == 'folder') {
            recursiveSoftDelete($id, $user_id);
        } else {
            $query = "UPDATE drive SET soft_delete = 1 WHERE id = '$id' && user_id = '$user_id' && soft_delete = 0 LIMIT 1";
            $queryResult = query($query);

            if(!$queryResult) {
                $info['success'] = false;
                $info['message'] = "Failed to soft delete the selected items.";
            }
        }
    }
    $info['success'] = true;
    $info['message'] = "File/Folder soft deleted successfully.";

    echo json_encode($info);
}

function handleHardDelete($info) {
    $ids = $_POST['id'] ?? [];
    $file_types = $_POST['file_type'] ?? [];
    $user_id = $_SESSION['RAIN_USER']['id'];
    $successCount = 0;

    foreach (array_map(null, $ids, $file_types) as [$id, $file_type]) {
        if ($file_type === 'folder') {
            recursiveHardDelete($id, $user_id);
        } elseif ($file_type === 'file') {
            // For files, perform the hard delete
            $query = "DELETE FROM drive WHERE id = '$id' AND user_id = '$user_id' AND soft_delete = 1 LIMIT 1";
            $queryResult = query($query);

            if ($queryResult) {
                $successCount++;
            }
        }
    }

    if ($successCount === count($ids)) {
        $info['success'] = true;
        $info['message'] = "File/Folder permanently deleted.";
    } else {
        $info['success'] = false;
        $info['message'] = "Missing 'id' or 'file_type' in the request.";
    }

    echo json_encode($info);
}