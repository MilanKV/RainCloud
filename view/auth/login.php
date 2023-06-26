<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../assets/css/login_signup.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <title>Login</title>
    </head>
    <body>
        <div class="container-login-register">
            <div class="cover">
                <div class="front">
                <img class="image" src="../../assets/image/undraw_my_personal_files_re_3q0p.svg" alt="">
                    <div class="text">
                        <span class="text-1">Need a shelter for your files?<br>Look no further than RainCloud storage,</span>
                        <span class="text-2">the waterproof solution to keep your data dry and hilarious cat videos intact.</span>
                    </div>
                </div>
            </div>
            <div class="forms">
                <div class="form-content">
                    <div class="login-form">
                        <div class="title">Login</div>
                        <form onsubmit="login.submit(event)" method="post" enctype="multipart/form-data">
                            <div class="input-boxes">
                                <div class="input-box">
                                    <i class="fas fa-envelope"></i>
                                    <input id="email" type="email" name="email" placeholder="Enter your email" value="">
                                </div>
                                <span class="error-message error-email hidden"></span>
                                <div class="input-box">
                                    <i class="fas fa-lock"></i>
                                    <input id="password" type="password" name="password" placeholder="Enter your password">
                                </div>
                                <span class="error-message error-password hidden"></span>
                                <div class="containerEr error-container hidden">Wrong EMAIL AND W</div>
                                <div class="text">
                                    <a class="btn btn-link" href="#">
                                        Forgot Password?
                                    </a>
                                </div>
                                <div class="button input-box">
                                    <input class="log-button" type="submit" value="LOGIN">
                                </div>
                                <div class="text sign-up-text">Don't have an account?<a class="links" href="../auth/signup.php"> Create Account</a></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script src="../../assets/js/login_signup.js"></script>
</html>