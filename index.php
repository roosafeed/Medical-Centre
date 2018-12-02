<?php
    $GLOBALS["page"] = "Welcome";
    include_once("Includes/header.php");

    if(isloggedin())
    {
        header("Location: home.php");
    }

    $error = "";

    if(isset($_POST['login']))
    {
        $email = $_POST['username'];
        $password = $_POST['password'];

        if(empty($email) || empty($password))
        {
            $error = "Please fill in the fields";
        }
        else
        {
            $query = "SELECT id, fname FROM users WHERE LOWER(email) = LOWER(?) AND password = SHA(?) LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
            $stmt->store_result();

            if($stmt->num_rows == 1)
            {
                $stmt->bind_result($id, $fname);
                $stmt->fetch();
                $stmt->close();

                $_SESSION['userid'] = $id;
                $_SESSION['fname'] = $fname;
                $_SESSION['email'] = $email;

                header("Location: home.php");
            }
            else
            {
                $stmt->close();
                $error = "Invalid email or password";
            }
        }
        
    }
?>



<form id="login-form" method="post" action="index.php"> 
    <h1>NITC HEALTH CENTER</h1>       
    <h3>Login to continue...</h3>
    <p class="error" style="color: #f00;"><?php echo $error; ?></p>
    <input type="text" name="username" placeholder="Email" title="Email" required /> <br />
    <input type="password" name="password" placeholder="Password" title="Password" required /> <br />
    <input type="submit" name="login" value="Login" />
    <br /><br />
    <p>New users, contact the health centre authorities to create an account.</p>
</form>

    


<?php
    include_once("Includes/footer.php");
?>