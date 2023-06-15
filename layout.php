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
                    <a href="#">
                        <span class="link_name">Home</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="link_name">Favorites</span>
                    </a>
                </li>
                <li>
                    <a href="#"> 
                        <span class="link_name">Shared</span>
                    </a>
                </li>
                <li>
                    <a href="#"> 
                        <span class="link_name">Deleted Files</span>
                    </a>
                </li>      
            </ul>
            <div class="cloud-space">
                <span class="link_name">Cloud Space</span>
                <div class="loader"></div>
                <div class="space">2GB of 15GB used
                    <a href="">
                        <i class='bx bx-plus-circle'></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="main">
            <?php
            require_once '../RainCloud/pages/home.php';
            ?>
        </div>
    </div>
    <script src="./assets/js/home.js"></script>
</body>
</html>