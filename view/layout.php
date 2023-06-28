<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/layout.css">
    <link rel="stylesheet" href="../assets/css/home.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/dd14b1184b.js" crossorigin="anonymous"></script>
    <title>RainCloud</title>
</head>
<body>
    <div class="container">
        <div class="nav">
            <a class="logo-title" href="?page=home">
                <i class='bx bx-cloud-light-rain'></i>
                <span class="title">RainCloud</span>
            </a>
            <div class="utilities">
                <div class="search">
                    <div class="search-centered">
                        <label>
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(115, 108, 100, 1)">
                                    <path d="M19.023 16.977a35.13 35.13 0 0 1-1.367-1.384c-.372-.378-.596-.653-.596-.653l-2.8-1.337A6.962 6.962 0 0 0 16 9c0-3.859-3.14-7-7-7S2 5.141 2 9s3.14 7 7 7c1.763 0 3.37-.66 4.603-1.739l1.337 2.8s.275.224.653.596c.387.363.896.854 1.384 1.367l1.358 1.392.604.646 2.121-2.121-.646-.604c-.379-.372-.885-.866-1.391-1.36zM9 14c-2.757 0-5-2.243-5-5s2.243-5 5-5 5 2.243 5 5-2.243 5-5 5z"></path>
                                </svg>
                            </span>
                            <input type="text" placeholder="Search">
                        </label>
                    </div>
                </div>
                <div class="user-panel">
                    <div class="utility-item hidden">
                        <button class="util-btn"></button>
                    </div>
                    <div class="utility-item hidden">
                        <button class="util-btn"></button>
                    </div>
                    <div class="utility-item">
                        <button id="util-btn" class="util-btn">
                            <div class="button-content">
                                <div class="avatar">
                                    <img src="../assets/image/avatar.png" alt="">
                                </div>
                            </div>
                        </button>
                        <div id="prof-Menu" class="prof-Menu hidden">
                            <div class="menu-head-item">
                                <div class="user-summary">
                                    <div class="info">
                                        <button class="photo-btn">
                                            <div class="button-content">
                                                <div class="avatar">
                                                    <img src="../assets/image/avatar.png" alt="">
                                                </div>
                                            </div>
                                        </button>
                                        <div class="user-name">
                                            <span id="user_name" class="user_name">User</span>
                                            <div class="user-email">
                                                <span class="user_email">Email</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="upgrade">
                                        <span class="text">Your account has 15 GB storage</span>
                                        <button class="upgrade-btn">
                                            <span class="upgrade-text">Upgrade</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="menu-item-body">
                                <a class="link-menu-drop" href="#">
                                    <div class="menu-item-content">
                                        <span class="menu-item-title">Settings</span>
                                    </div>
                                </a>
                                <a class="link-menu-drop" href="#">
                                    <div class="menu-item-content">
                                        <span class="menu-item-title">Sign out</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sidebar">
            <ul>
                <li>
                    <a href="?page=home">
                        <span class="link_name">Home</span>
                    </a>
                </li>
                <li>
                    <a href="?page=favorites">
                        <span class="link_name">Favorites</span>
                    </a>
                </li>
                <li>
                    <a href="?page=shared"> 
                        <span class="link_name">Shared</span>
                    </a>
                </li>
                <li>
                    <a href="?page=deleted"> 
                        <span class="link_name">Deleted Files</span>
                    </a>
                </li>      
            </ul>
            <div class="cloud-space">
                <span class="link_name">Cloud Space</span>
                <div class="progress-space"></div>
                <div class="space">2GB of 15GB used
                    <a href="">
                        <i class='bx bx-plus-circle'></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="main">
            <?php
            ob_start(); // Start output buffering
            // Default home if no page parameter is provided
            $page = $_GET['page'] ?? 'home';

            if(!isset($_GET['page']) && empty($_SERVER['QUERY_STRING']))
            {
                header("Location: layout.php?page={$page}");
                ob_end_flush(); // End output buffering and send headers
            }
            // Define an array of allowed pages
            $allowedPages = ['home', 'favorites', 'shared', 'deleted'];
            if(in_array($page, $allowedPages)) 
            {
                $contentFile = "../view/pages/{$page}.php";
                if(file_exists($contentFile))
                {
                    require_once $contentFile;
                } else {
                    echo "Page not found.";
                } 
            } else {
                echo "Invalid page.";
            }
            ?>
        </div>
        <div class="file-details-panel">
            <div class="title-container">
                <span class="file-title">
                    File Details
                </span>
            </div>
            <div class="header-container"></div>
            <div class="no-file-checked">
                <i class='bx bx-info-square'></i>
                <span class="title">
                    No File was Selected
                </span>
            </div>
            <div class="body-container hidden">
                <div class="info">
                    <div class="info-header">
                        <div class="left-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);">
                                <path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path>
                                <path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path>
                            </svg>
                        </div>
                        <div class="right-title">
                            <span>Info</span>
                        </div>
                    </div>
                </div>
                <div class="file-detail">
                    <div class="icon-container">
                        <a href="">
                            <img id="file_image" src="">
                            <i id="file_icon"></i>
                        </a>
                    </div>
                    <div class="details">
                        <span>Properties</span>
                        <div id="file_name" class="properties">
                            <span class="title">Name</span>
                            <span class="file_name"></span>
                        </div>
                        <div id="size" class="properties">
                            <span class="title">Size</span>
                            <span class="size"></span>
                        </div>
                        <div id="type" class="properties">
                            <span class="title">Type</span>
                            <span class="type"></span>
                        </div>
                        <div id="created" class="properties">
                            <span class="title">Added</span>
                            <span class="date_created"></span>
                        </div>
                        <div id="updated" class="properties">
                            <span class="title">Modified</span>
                            <span class="date_updated"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/home.js"></script>
</body>
</html>