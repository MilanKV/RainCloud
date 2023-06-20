<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/home.css">
    <title>RainCloud</title>
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="#">
                <i class='bx bx-cloud-light-rain'></i>
                <span class="title">RainCloud</span>
            </a>
            <div class="search">
                <label>
                    <input type="text" placeholder="Search here">
                    <i class='bx bx-search'></i>
                </label>
            </div>
            <div class="profile-details">
                <img src="assets/image/avatar.png" alt="">
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
            // Default home if no page parameter is provided
            $page = $_GET['page'] ?? 'home';

            if(!isset($_GET['page']) && empty($_SERVER['QUERY_STRING']))
            {
                header("Location: layout.php?page={$page}");
            }
            // Define an array of allowed pages
            $allowedPages = ['home', 'favorites', 'shared', 'deleted'];
            if(in_array($page, $allowedPages)) 
            {
                $contentFile = "../RainCloud/pages/{$page}.php";
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
    </div>
    <script src="./assets/js/home.js"></script>
</body>
</html>