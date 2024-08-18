<?php

include('database.php');


if (isset($_POST['create'])) {

    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $birthday = $_POST['birthday'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    
    if ($password !== $confirmPassword) {
        echo '<script>alert("Passwords do not match!");</script>';
    } else {
        
        $checkUsernameQuery = "SELECT * FROM Admin_account WHERE Username = '$username'";
        $result = mysqli_query($connection, $checkUsernameQuery);

        if (mysqli_num_rows($result) > 0) {
            
            echo '<script>alert("Username already exists! Please choose a different one.");</script>';
        } else {
            
            $query = "INSERT INTO Admin_account (firstName, lastName, Birthday, Username, Password) 
                      VALUES ('$firstName', '$lastName', '$birthday', '$username', '$password')";

            
            if (mysqli_query($connection, $query)) {
                echo '<script>alert("Account created successfully!");</script>';
                echo '<script>window.location.href = "/system/php/index.php"</script>';
            } else {
                echo "Error: " . mysqli_error($connection);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create account</title>
    <link rel="stylesheet" href="create_account.css">
</head>
<body>
    <div class="main">
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
            <div class="form">
                <center>
                    <div class="login">
                        <form action="" method="post">
                            <div class="name2">
                                <input type="text" class="fname2 text1" name="first_name" placeholder="First Name:" required>
                                <input type="text" class="lname2 text1" name="last_name" placeholder="Last Name:" required>
                            </div>
                            <input class="age text1" type="date" name="birthday" placeholder="Birthday:" required>
                            <input class="email text1" type="text" name="username" placeholder="Username:" required>
                            <input class="password text1" type="password" name="password" placeholder="Create Password:" required>
                            <input class="password text1" type="password" name="confirm_password" placeholder="Confirm Password:" required>
                            <button type="submit" class="create" name="create"><p class="c">Create</p></button>
                        </form>
                    </div>
                </center>
            </div>
        </div>
    </div>
</body>
</html>
