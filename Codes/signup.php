<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Sign Up</title>
    <link rel="stylesheet" href="signUp.css">
</head>
<body class="w-full h-full">
    <div class="container w-full h-full">
        <h1 class="text-4xl font-bold">Sign Up</h1><br>
        <div class="divider bg-zinc-500 h-0.5 w-100 bg-red-500 m-4"></div>
        <?php
            require_once("conn.php");

            $nameErr = $emailErr = $mobilenoErr = $usernameErr = $passwordErr = $passkeyErr = $passkeyvalueErr = $agreeErr = "";  
            $name = $email = $mobileno = $username = $password = $passkey = $passkeyvalue = "";
            
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                function input_data($data) {
                    return htmlspecialchars(stripslashes(trim($data)));
                }

                // Validate Name
                if (empty($_POST["name"])) {
                    $nameErr = "Name is required";
                } else {
                    $name = input_data($_POST["name"]);
                    if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
                        $nameErr = "Only alphabets and white space allowed";
                    }
                }

                // Validate Email
                if (empty($_POST["email"])) {
                    $emailErr = "Email is required";
                } else {
                    $email = input_data($_POST["email"]);
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $emailErr = "Invalid email format";
                    }
                }

                // Validate Mobile Number
                if (empty($_POST["mobileno"])) {
                    $mobilenoErr = "Mobile number is required";
                } else {
                    $mobileno = input_data($_POST["mobileno"]);
                    if (!preg_match("/^[0-9]{10}$/", $mobileno)) {
                        $mobilenoErr = "Mobile number must be exactly 10 digits";
                    }
                }

                // Validate Username and Check if Exists
                if (empty($_POST["username"])) {
                    $usernameErr = "Username is required";
                } else {
                    $username = input_data($_POST["username"]);
                    $query = "SELECT * FROM user WHERE username = '$username'";
                    $result = mysqli_query($conn, $query);
                    if (mysqli_num_rows($result) > 0) {
                        $usernameErr = "Username already taken";
                    }
                }

                // Validate Password
                if (empty($password=$_POST["password"])) {  
                    $passwordErr = "Password is required";  
                    } else {  
                    $password = input_data($_POST["password"]);  
                    if (!preg_match ("/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{4,20}$/", $password) ) {  
                    $passwordErr = "Match the following condition please!!";  
                    }  
                     
            }

                // Validate Passkey Selection
                if (empty($_POST["passkey"])) {
                    $passkeyErr = "Select a passkey option";
                } else {
                    $passkey = input_data($_POST["passkey"]);
                }

                // Validate Passkey Value
                if (empty($_POST["passkeyvalue"])) {
                    $passkeyvalueErr = "Enter passkey value";
                } else {
                    $passkeyvalue = input_data($_POST["passkeyvalue"]);
                }

                // Check Agreement
                if (!isset($_POST["agree"])) {
                    $agreeErr = "You must agree to the terms of service";
                }

                // If no errors, insert into database
                if ($nameErr == "" && $emailErr == "" && $mobilenoErr == "" && $usernameErr == "" && $passwordErr == "" && $passkeyErr == "" && $passkeyvalueErr == "" && $agreeErr == "") {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $query = "INSERT INTO user (name, mobileno, email, username, password, passkey, passkeyval, creation_date) 
                              VALUES ('$name', '$mobileno', '$email', '$username', '$hash', '$passkey', '$passkeyvalue', CURRENT_TIMESTAMP)";
                    
                    if (mysqli_query($conn, $query)) {
                        header("Location: redirect.php");
                        exit();
                    } else {
                        echo "Error: " . mysqli_error($conn);
                    }
                }
            }
        ?>

        <div class="form gap-10">
        <form method="post" class=" flex flex-col gap-4" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="name flex align-center justify-around w-4/4">
            <label for="name" class="w-1/3">Name</label>
            <input type="text" name="name" id="name" class="rounded-lg" value="<?php echo $name; ?>" placeholder="Enter your name"/>
            <span class="error">* <?php echo $nameErr; ?> </span>
            </div>

            <div class="mobNo flex align-center justify-around w-4/4">
            <label for="mobileno" class="w-1/3">Mobile No.</label>
            <input type="text" name="mobileno" id="mobileno" class="rounded-lg" value="<?php echo $mobileno; ?>" placeholder="Enter your mobile number"/>
            <span class="error">* <?php echo $mobilenoErr; ?> </span>
            </div>

            <div class="email  flex align-center justify-around">
            <label for="email" class="w-1/3">Email</label>
            <input type="text" name="email" id="email" class="rounded-lg" value="<?php echo $email; ?>" placeholder="Enter your email address"/>
            <span class="error">* <?php echo $emailErr; ?> </span>
            </div>

            <div class="username flex align-center justify-around">
<label for="username" class="w-1/3">Username</label>
            <input type="text" name="username" id="username" class="rounded-lg" value="<?php echo $username; ?>" placeholder="Enter your username"/>
            <span class="error">* <?php echo $usernameErr; ?> </span>
</div>

            <div class="passwd flex align-center justify-around">
                <label for="password" class="w-1/3">Password</label>
                <input type="password" name="password" id="password" class="rounded-lg" value="<?php echo $password; ?>" placeholder="Enter your password"/>
                <span class="error">* <?php echo $passwordErr; ?> </span>
            </div>
            <div class="passwd-info">
            <span class="error text-red-600 font-semibold">(One Uppercase & lowercase & number & Uniquevalue(@#$%^&*-) is required..) </span><br>
            </div><div class="h-1 "></div>

            <div class="passkey flex align-center justify-around">
            <label for="passkey" class="w-1/3">PassKey (For Password Recovery)</label>
            <select name="passkey" id="passkey">
                <option value="" class="text-black">--- Choose a passkey ---</option>
                <option value="fav_teacher" class="text-black" <?php if ($passkey == "fav_teacher") echo "selected"; ?>>Favorite Teacher</option>
                <option value="fav_movie" class="text-black" <?php if ($passkey == "fav_movie") echo "selected"; ?>>Favorite Movie</option>
                <option value="fav_food" class="text-black" <?php if ($passkey == "fav_food") echo "selected"; ?>>Favorite Food</option>
            </select>
            <span class="error">* <?php echo $passkeyErr; ?> </span>
            </div><div class="h-2"></div>

            <div class="passkey-val flex align-center justify-around">
            <label for="passkeyvalue" class="w-1/3">PassKey Value</label>
            <input type="text" name="passkeyvalue" id="passkeyvalue" class="rounded-lg" value="<?php echo $passkeyvalue; ?>" placeholder="Enter your passkey value"/>
            <span class="error">* <?php echo $passkeyvalueErr; ?> </span>
            </div>

            <div class="terms-con flex align-center justify-center gap-2 mb-8">
            <input type="checkbox" name="agree" <?php if (isset($_POST['agree'])) echo "checked"; ?>> I agree to the Terms of Service
            <span class="error">* <?php echo $agreeErr; ?> </span>
            </div>

            <div class="button flex align-center justify-center mt-6">
            <div class="btn bg-red-800 w-2/4 flex align-center justify-center rounded-lg">
                <button type="submit" class="p-10"  name="submit">Sign Up</button>
            </div>
            </div>
        </form>
        </div>
    </div>
</body>
</html>
