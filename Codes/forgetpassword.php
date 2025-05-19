
<?php
require_once("conn.php");

$usernameErr = $passkeyErr = $passkeyvalueErr = "";  
$username = $passkeyvalue = $passkey = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = input_data($_POST["username"]); 
    }

    if (empty($passkey=$_POST["passkey"])){  
        $passkeyErr = "Select Passkey Option..";  
        } else {  
        $passkey = input_data($_POST["passkey"]);  
        }


        if (empty($_POST['passkeyvalue'])){  
            $passkeyvalueErr = "Please fill passkey value..";  
            } else {  
            $passkeyvalue = input_data($_POST["passkeyvalue"]);  
            }

    if ($usernameErr == "" && $passkeyErr == "" && $passkeyvalueErr == "") {
        $query = "SELECT * FROM user WHERE BINARY username = '$username'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if($row['passkey'] == $passkey)
            {
            if ($row['passkeyval'] == $passkeyvalue) {
                header("Location: resetpassword.php?username=" .$username);
                exit();
            } else {
                $passkeyvalueErr = "Passkey Value is incorrect";
            }
        }
        else
        {
            $passkeyErr = "Passkey is incorrect";   
        }
        } else {
            $usernameErr = "Username not found";
        }
    }
}

function input_data($data) {  
    $data = trim($data);  
    $data = stripslashes($data);  
    $data = htmlspecialchars($data);  
    return $data;  
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <!-- <script src="https://unpkg.com/@tailwindcss/browser@4"></script> -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <!-- <link rel="stylesheet" href="signUp.css"> -->
    <style>
        .error { color: #FF0000; }
    </style>
</head>
<body class="bg-gray-100 w-full h-screen flex justify-center items-center bg-zinc-700">

    <div class="w-full max-w-md text-white  bg-zinc-600 p-8 rounded-lg shadow-lg ">
        <h1 class="text-3xl font-bold text-center text-white-800 mb-6">Forget Password?</h1>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-6">
            
            <div class="form-group">
                <label for="username" class="block text-white-700">Username</label>
                <input type="text" name="username" id="username" class="w-full p-3 border border-gray-300 rounded-lg" value="<?php echo $username; ?>" placeholder="Enter your username" />
                <span class="error text-sm"><?php echo $usernameErr; ?></span>
            </div>
            
            <div class="form-group">
                <label for="passkey" class="block text-white-700">PassKey</label>
                <select name="passkey" id="passkey" class="w-full p-3 border border-gray-300 rounded-lg">
                    <option value="" class="text-black">--- Choose a passkey ---</option>
                    <option value="fav_teacher" class="text-black" <?php if (isset($passkey) && $passkey == "fav_teacher") echo "selected"; ?>>Favorite Teacher</option>
                    <option value="fav_movie" class="text-black" <?php if (isset($passkey) && $passkey == "fav_movie") echo "selected"; ?>>Favorite Movie</option>
                    <option value="fav_food" class="text-black" <?php if (isset($passkey) && $passkey == "fav_food") echo "selected"; ?>>Favorite Food</option>
                </select>
                <span class="error text-sm"><?php echo $passkeyErr; ?></span>
            </div>
            
            <div class="form-group">
                <label for="passkeyvalue" class="block text-white-700">PassKey Value</label>
                <input type="text" name="passkeyvalue" id="passkeyvalue" class="w-full p-3 border border-gray-300 rounded-lg" value="<?php echo $passkeyvalue; ?>" placeholder="Enter your passkey value" />
                <span class="error text-sm"><?php echo $passkeyvalueErr; ?></span>
            </div>
            
            <!-- Submit Button -->
            <div class="flex justify-center">
                <button type="submit" name="submit" class="w-full py-3 bg-blue-500 text-white font-bold rounded-lg hover:bg-blue-600 transition duration-300">
                    Next
                </button>
            </div>

        </form>
    </div>

</body>
</html>