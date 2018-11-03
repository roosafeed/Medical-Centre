<?php
    include_once("initiate.php");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?php echo $GLOBALS["page"]; ?> - NITC Health Centre</title>

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="/Styles/Style.css" rel="stylesheet" type="text/css" />
        <script src="/JS/jquery3.3.1min.js"></script>
        <link href="/JS/autocomplete/jquery-ui.css" rel="stylesheet" type="text/css" />
        <script src="JS/autocomplete/jquery-ui.min.js"></script>
        
    </head>
    <body>

        <div id="loading"></div>
        <?php
            if(isloggedin())
            {
                include_once("navbar.php");
            }
        ?>
        <div id="body">
