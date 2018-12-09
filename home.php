<?php
    $GLOBALS["page"] = "Home";
    include_once("Includes/header.php");

    if(!isloggedin())
    {
        header("Location: index.php");
    }

    if(isAdmin() || isHCstaff())
    {
        include_once("Includes/admin.php");
    }
    else
    {
        include_once("Includes/user.php");
    }

    include_once("Includes/footer.php");

?>

