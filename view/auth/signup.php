<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/login-signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Sign Up</title>
</head>
<body>
    <div class="container-login-register">
        <div class="cover">
            <div class="front">
            <img class="image" src="../../assets/image/undraw_folder_re_apfp.svg" alt="">
                <div class="text">
                    <span class="text-1">Unleash the power of<br>RainCloud storage</span>
                    <span class="text-2">and watch your files thrive.</span>
                </div>
            </div>
        </div>
        <div class="forms">
            <div class="form-content">
                <div class="signup-form">
                    <div class="title">Create Account</div>
                    <form method="post" action="">
                        
                        <div class="input-boxes">
                            <div class="input-box">
                                <i class="fas fa-user"></i>
                                <input id="name" type="text" name="name" placeholder="Enter your name" value="" required autocomplete="name" autofocus>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-envelope"></i>
                                <input id="email" type="email" name="email" placeholder="Enter your email" value="" required autocomplete="email" autofocus>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input id="password" type="password" name="password" placeholder="Enter your password" required autocomplete="new-password">
                            </div>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input id="password-confirm" type="password" name="password_confirmation" placeholder="Confirm your password"  required autocomplete="new-password">
                            </div>
                            <div class="button input-box">
                                <input type="submit" value="Submit">
                            </div>
                            <div class="text sign-up-text">Already have an account? <a class="links" href="">Login</a></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>