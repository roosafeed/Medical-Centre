<?php
    ini_set("log_errors", 1);
    ini_set("error_log", "log.txt");   
    error_reporting(E_ALL);
    ini_set('display_errors', '1'); 

    session_start();

    include_once("DBsettings.php");

    $conn = new mysqli($db_server, $db_user, $db_password, $db_name);
    if($conn->connect_error)
    {
        die("Database connection failed. Contact admins. Error: " . $conn->connect_error);
    }

    include_once("db.php");

    function isloggedin()
    {
        return isset($_SESSION["email"]);
    }

    function isAdmin()
    {
        if(isloggedin())
        {
            global $conn;
            $q = "SELECT role FROM user_roles R INNER JOIN users_in_roles UR ON R.id = UR.role_id INNER JOIN users U ON U.id = UR.user_id WHERE U.id = ? AND R.id = 1";
            $queryRole = $conn->prepare($q);
            $queryRole->bind_param("d", $_SESSION["userid"]);
            $queryRole->execute();
            $queryRole->store_result();
            if($queryRole->num_rows == 1)
            {
                $queryRole->close();
                return TRUE;
            }
            else
            {
                $queryRole->close();
                return FALSE;
            }
            
        }
        else
        {
            return FALSE;
        }
    }

    function isNurse()
    {
        if(isloggedin())
        {
            global $conn;
            $q = "SELECT role FROM user_roles R INNER JOIN users_in_roles UR ON R.id = UR.role_id INNER JOIN users U ON U.id = UR.user_id WHERE U.id = ? AND R.id = 3";
            $queryRole = $conn->prepare($q);
            $queryRole->bind_param("d", $_SESSION["userid"]);
            $queryRole->execute();
            $queryRole->store_result();
            if($queryRole->num_rows == 1)
            {
                $queryRole->close();
                return TRUE;
            }
            else
            {
                $queryRole->close();
                return FALSE;
            }
        }

        else
        {
            return FALSE;
        }
    }

    function isDoc()
    {
        if(isloggedin())
        {
            global $conn;
            $q = "SELECT role FROM user_roles R INNER JOIN users_in_roles UR ON R.id = UR.role_id INNER JOIN users U ON U.id = UR.user_id WHERE U.id = ? AND R.id = 2";
            $queryRole = $conn->prepare($q);
            $queryRole->bind_param("d", $_SESSION["userid"]);
            $queryRole->execute();
            $queryRole->store_result();
            if($queryRole->num_rows == 1)
            {
                $queryRole->close();
                return TRUE;
            }
            else
            {
                $queryRole->close();
                return FALSE;
            }
        }

        else
        {
            return FALSE;
        }
    }

    function isHCstaff()
    {
        return isNurse() || isDoc();   
    }
?>