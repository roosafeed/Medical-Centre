<?php
    include_once("header.php");

    if(isAdmin() || isHCstaff())
    {
        header("Location: /home.php");
    }
?>



<?php
    include_once("footer.php");
?>
