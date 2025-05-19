<?php
session_start();
require_once("conn.php");

$usernameErr = $passwordErr = $captchaErr = "";
$username = $password = "";

function input_data($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = input_data($_POST["username"]);
    $password = input_data($_POST["password"]);
    $captcha_input = input_data($_POST["captcha"]);

    if (empty($username)) $usernameErr = "Username is required";
    if (empty($password)) $passwordErr = "Password is required";
    if (empty($captcha_input)) {
        $captchaErr = "Captcha is required";
    } elseif ($captcha_input !== $_SESSION['captcha_code']) {
        $captchaErr = "Captcha mismatched, please try again";
    }

    if (empty($usernameErr) && empty($passwordErr) && empty($captchaErr)) {
        $username = mysqli_real_escape_string($conn, $username);
        $query = "SELECT * FROM user WHERE BINARY username = '$username'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                $_SESSION['userid'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['mono'] = $row['mobileno'];
                $_SESSION['email'] = $row['email'];
              
                header("Location: dashboard.php");
                exit;
            } else {
                $passwordErr = "Incorrect username or password";
            }
        } else {
            $usernameErr = "Incorrect username or password";
        }
    }
}

$captcha_code = substr(md5(mt_rand()), 0, 6);
$_SESSION['captcha_code'] = $captcha_code;

function generateCaptcha($code) {
    $image = imagecreate(100, 30);
    $background_color = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 0, 0, 0);
    imagestring($image, 5, 10, 8, $code, $text_color);
    ob_start();
    imagejpeg($image);
    $image_data = ob_get_clean();
    imagedestroy($image);
    return base64_encode($image_data);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Login Page</title>
    <style>
        body {
    background-color: #4b5563;
    color: white;
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

.container {
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background-color: #4b5563;
    border-radius: 8px;
}

input[type="text"], input[type="password"], input[name="captcha"] {
    color: white;
    background-color: transparent;
    border: 1px solid gray;
    outline: none;
    padding: 10px;
    border-radius: 8px;
    width: 100%;
}

button {
    color: white;
    background-color: transparent;
    border: 1px solid gray;
    outline: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
}

button:hover {
    background-color: rgba(75, 85, 99, 0.1);
}

.error {
    color: red;
    font-size: 12px;
}

.links a {
    color: white;
}

.divider {
    margin: 20px 0;
    border-top: 1px solid #4b5563;
}

.cap-img img {
    border: 1px solid gray;
    border-radius: 8px;
}

h1 {
    color: white;
}
    </style>
</head>
<body>

<div class="container">
    <center><h1 class="text-4xl font-semibold">Login</h1></center>
    <center><div class="divider h-0.5 w-2/4 bg-zinc-500"></div></center>
    <div class="form w-full h-full flex justify-center align-center">
    <form method="post" class="flex flex-col gap-4" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    
    <div class="username flex align-center justify-around w-4/4">
        <label for="username" class="w-1/3">Username:</label>
        <input type="text" name="username" class="rounded-lg" id="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Enter your username"/>
        <span class="error"><?php echo $usernameErr; ?></span>
    </div>
        
    <div class="password flex align-center justify-around w-4/4">
    <label for="password" class="w-1/3">Password:</label>
        <input type="password" name="password" class="rounded-lg"  id="password" value="<?php echo htmlspecialchars($password); ?>" placeholder="Enter your password"/>
        <span class="error"><?php echo $passwordErr; ?></span>
    </div>
        
    <div class="captcha flex-col align-center justify-center w-4/4">
    <label for="captcha" class="w-1/3">Enter Captcha:</label>
        <input type="text" id="captcha" class="rounded-lg mb-6"  name="captcha" placeholder="Enter Captcha">
        <span class="error"><?php echo $captchaErr; ?></span>
        <div class="cap-img w-4/4 flex align-center justify-center">
            <img src="data:image/jpeg;base64,<?php echo generateCaptcha($_SESSION['captcha_code']); ?>" alt="Captcha Image">
        </div>
    </div>

       <div class="btn w-4/4 flex align-center justify-center rounded-lg">
       <button type="submit" name="submit">Sign in</button>
       </div>
    </form> 
    </div>

    <div class="links flex flex-col align-center justify-center text-white gap-2">
    <center><a href="forgetpassword.php">Forgot password?</a> </center>  
    <center><a href="signup.php">Are you new? Create an Account</a></center>
    </div>
</div>

</body>
</html>