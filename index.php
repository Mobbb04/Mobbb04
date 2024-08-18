<?php
// Include the database connection
include('database.php');

// Start a session to store user information upon successful login
session_start();

// Check if the form is submitted
if (isset($_POST['client'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the SQL query to check if the credentials are correct
    $query = "SELECT * FROM Admin_account WHERE Username = '$username'";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Verify the password (if you use password hashing)
        // if (password_verify($password, $row['Password'])) {
        if ($password === $row['Password']) { // Use this if passwords are stored in plain text
            // Store user information in session
            $_SESSION['username'] = $row['Username'];
            $_SESSION['firstName'] = $row['firstName'];
            
            // Redirect to the dashboard or another page
            echo '<script>alert("Login successful!"); window.location.href="dashboard.php";</script>';
        } else {
            echo '<script>alert("Incorrect password. Please try again.");</script>';
        }
    } else {
        echo '<script>alert("Username not found. Please try again.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>index</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="navbar">
        <div class="icon">
            <img src="logo.png" alt="">
        </div>
        <div class="menu">
            <ul class="list">
                <li><a href="#">HOME</a></li>
                <li><a href="#">ABOUT</a></li>
                <li><a href="#">SERVICE</a></li>
                <li><a href="#">DESIGN</a></li>
                <li><a href="#">CONTACT</a></li>
            </ul>
        </div>
    </div> 
    <div class="content">
        <div class="title">
            <div class="info">
                <h1>Stock.Inventory.System</h1>
                <p>We Stock All Goods</p>
            </div>
        </div>
        <div class="form">
            <h2>LOGIN</h2>
            <div class="login">
                <form action="" method="post">
                    <input class="username" type="text" name="username" placeholder="Username" required>
                    <input class="password" type="password" name="password" placeholder="Enter Password" required>
                    <input class="btnn1" type="submit" name="client" value="LOGIN">
                </form>
            </div>
            <p class="link">Don't have an account?<br>
                <a href="create_account.php">Sign up</a> here</p>
            <hr class="line">
        </div>
    </div>
</body>
</html>
