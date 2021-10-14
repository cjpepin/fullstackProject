 <!DOCTYPE html>
<?php session_start() ?>
<html>
    <head>
    <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- <link rel="stylesheet" href="main.css"> -->
    </head>
    <body>
    <?php
        require "database.php";
        $allErr = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST["forgotpass"])){
                header("location: resetpass.php");
            }
            //Ensures no dangerous user input
            function test_in($input){
                $input = trim($input);
                $input = stripslashes($input);
                $input = htmlspecialchars($input);
                return $input;
            }
            //create session/local variables
            $user =  test_in($_POST["user"]);
            $pwd_guess = test_in($_POST["pass"]);
            
            // Use a prepared statement
            $stmt = $mysqli->prepare("SELECT COUNT(*), user, pass FROM users WHERE user=?");

            // Bind the parameter
            $stmt->bind_param('s', $user);
            $stmt->execute();

            // Bind the results
            $stmt->bind_result($cnt, $user_id, $pwd_hash);
            $stmt->fetch();
            // Compare the submitted password to the actual password hash

            if (!($cnt == 1 && password_verify($pwd_guess, $pwd_hash))){
                $allErr = "Incorrect username/password";
            } elseif (empty($user)) {
                $userErr = "Username is required.";
            } elseif (empty($pwd_guess)){
                $allErr = "Password is required";                       
            } elseif($cnt == 1 && password_verify($pwd_guess, $pwd_hash)){
                // Login succeeded!
                $_SESSION['user'] = $user;
                $user = $_SESSION['user'];
                echo("It worked! $user");
                header("location: index.php");
            }
            

            
            

            
        }

    ?>
        <form name="input" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">

            <div class="input-wrapper">
                Username: <input type="text" name="user"> 
            
            </div>
            <div class="input-wrapper">
                Password: <input type="password" name="pass"> 
                
                <span class="error"> <?php echo htmlentities($allErr)?> </span>
            </div>
            <input type="submit" value="Submit">
            
           
        </form>
        <!-- Send user to seperate site to create a new user account -->
        <form name="guest" action="forum.php" method="GET">
            <input type="submit" value="Enter as Guest">
        </form>
        <form name="forgotpass" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
            <input type="submit" value="Forgot Password?" name="forgotpass">
        </form>
        <form name="newUser" action="adduser.php" method="GET">
            <input type="submit" value="Create New User">
        </form>
    </body>
</html> 
