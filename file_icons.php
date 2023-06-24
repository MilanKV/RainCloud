<?php

function get_icon($type, $ext = null)
{
    $icons = [
        'image/jpeg' => '<i class="fa-regular fa-image fa-2xl"></i>',
        'image/gif' => '<i class="fa-regular fa-image fa-2xl"></i>',
        'audio/mpeg' => '<i class="fa-regular fa-file-audio fa-2xl"></i>',
        'video/x-matroska' => '<i class="fa-light fa-photo-film fa-2xl"></i>',
        'video/mp4' => '<i class="fa-light fa-photo-film fa-2xl"></i>',
        'folder' => '<i class="fa-solid fa-folder fa-2xl" style="color: #ffdf3d;"></i>',
        'text/plain' => '<i class="fa-regular fa-file-lines fa-2xl"></i>',
        'text/html' => '<i class="fa-brands fa-html5 fa-2xl"></i>',
        'application/vnd.openxmlformats-officedocument.word' => '<i class="fa-sharp fa-light fa-file-word fa-2xl"></i>',
        'files' => '<i class="fa-sharp fa-light fa-files fa-2xl"></i>',

        'application/octet-stream' => [
            'pdf' => '<i class="fa-sharp fa-light fa-file-pdf fa-2xl"></i>',
            'sql' => '<i class="fa-regular fa-file-code fa-2xl"></i>',
        ],
    ];

    if($type == 'application/octet-stream') {
        return $icons[$type][$ext] ?? '<i class="fa-sharp fa-light fa-file-exclamation fa-2xl"></i>';
    }

    return $icons[$type] ?? '<i class="fa-sharp fa-light fa-file-exclamation fa-2xl"></i>';
}