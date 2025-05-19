<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="log_in.css">
    <style>  
.error {color: #FF0001;}  
</style>
</head>
<body>
    <?php
    $passwordErr = "";  
    $password ="";

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
       if (empty($password=$_POST["password"])) {  
        $passwordErr = "Password is required";  
        } else {  
        $password = input_data($_POST["password"]);  
        if (!preg_match ("/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{4,20}$/", $password) ) {  
        $passwordErr = "Match the following condition please!!";  
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
    <div class="container">
<h1>Reset Password</h1>
<form method="post">
<label>New Password</label>
    <input type="password" name="password" />
    <span class="error"> <?php echo $passwordErr; ?> </span><br>
    <span class="error">(One Uppercase & lowercase & number & Uniquevalue(@#$%^&*-) is required..) </span><br><br>
    
    <button type="submit" name="submit">Submit</button>
</form>
</div>
<?php
require_once("conn.php");
$query="select * from user";
$res=mysqli_query($conn,$query);
$pass=$_REQUEST['username'];

if(isset($_POST['submit']))
{
    $password=$_POST['password'];
    $hash=password_hash($password,PASSWORD_DEFAULT);
    while($record=mysqli_fetch_assoc($res))
    {
        $query1="update user set password='$hash' where username='$pass'";
        mysqli_query($conn,$query1);
        echo "<script>
        alert('Password Successfully Changed..');
        window.location='login.php';
        </script>";    }
}
?>

</body>
</html>