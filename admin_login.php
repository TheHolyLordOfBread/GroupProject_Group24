<?php
session_start();

$db1="DatabaseForGroupProject.db";
$db = new SQLite3($db1);

$error = '';
if($_SERVER["REQUEST_METHOD"] == "POST") 
    {
    $username = $_POST['username'];
    $password1 = $_POST['password'];


   $stmt = $db->prepare("SELECT * FROM 'ADMIN' WHERE USERNAME = :username AND PASSWORD1 = :password");

if($stmt)
{   
$stmt->bindValue(':username', $username, SQLITE3_TEXT);
$stmt->bindValue(':password', $password1, SQLITE3_TEXT);

$result = $stmt->execute();
$row= $result->fetchArray(SQLITE3_ASSOC);
if($row)
    {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $row['USERNAME'];

        header("Location: admin_dashboard.php");
        exit();
    }
    else
        {
            $error = "Invalid username or password.";
        }
}   
   
}
    

?>



<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body {
            background-color: #090a0a;
            font-family: Arial, sans-serif;
            color: #0c0b0b;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;

            background: url('https://images.alphacoders.com/104/1049857.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .login-box {
            background: rgba(106, 144, 239, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px #adb5b3, 0 0 40px #adb5d3;
            text-align: center;
            width: 320px;
        }
        .login-box img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 15px;
            box-shadow: 0 0 15px #80e3ec;
        }
        .login-box h2 {
            color: #98a7f0;
            text-shadow: 0 0 10px #0f1212;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            outline: none;
            border-radius: 5px;
            background: #f0f0f0;
            color: #333;
            box-shadow: 0 0 10px #77e2f5 inset;
        }
        input[type="submit"] {
            background: #5100ff;
            color: #000;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }
        input[type="submit"]:hover {
<<<<<<< Updated upstream
            background: #00ffaa;
            box-shadow: 0 0 20px #00ffaa;
=======
            background: #00d9ff;
            box-shadow: 0 0 20px #00d9ff;
>>>>>>> Stashed changes
        }
        .error {
            color: #ff4d4d;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <img src="https://img.lawyerment.com/guide/cdn/1/218.gif" alt="NAHRIM Logo">
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" value="Login">
        </form>
        <?php if (isset($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    </div>
</body>
<<<<<<< Updated upstream
</html>
=======
</html>

>>>>>>> Stashed changes
