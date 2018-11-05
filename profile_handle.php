<?php
    include_once("Includes/initiate.php");

    if(!isloggedin())
    {
        header("Location: index.html");
    }

    if(isset($_POST["old-pass"]))
    {
        $old_pass = $_POST["old-pass"];
        $new_pass = $_POST["new-pass"];
        $con_pass = $_POST["new-pass-con"];

        if(empty($old_pass) || empty($new_pass) || empty($con_pass))
        {
            echo "Every field is required";
        }
        elseif($old_pass == $new_pass)
        {
            echo "Old and new passwords cannot be the same";
        }
        elseif($new_pass != $con_pass)
        {
            echo "New password and Confirm password does not match";
        }
        else
        {
            $qpchk = "SELECT id FROM users WHERE id = ? AND password = SHA(?)";
            $qpchk = $conn->prepare($qpchk);
            $qpchk->bind_param("ds", $_SESSION["userid"], $old_pass);
            $qpchk->execute();
            $qpchk->store_result();
            if($qpchk->num_rows != 1)
            {
                echo "Your old password is wrong";
            }
            else
            {
                $qupass = "UPDATE users SET password = SHA(?) WHERE id = ?";
                $qupass = $conn->prepare($qupass);
                $qupass->bind_param("sd", $new_pass, $_SESSION["userid"]);
                $qupass->execute();
                $qupass->store_result();
                if($qupass->affected_rows == 1)
                {
                    echo "Password changed successfully";
                }
                else
                {
                    echo "Error. Password not changed. Use old password.";
                }
            }
        }
    }

    if(isset($_POST["new-cont-mob"]))
    {
        $rel = $_POST["new-rel"];
        $fname = trim($_POST["new-cont-fname"]);
        $lname = trim($_POST["new-cont-lname"]);
        $mob = trim($_POST["new-cont-mob"]);
        if(!is_numeric($mob))
        {
            echo "Enter a valid mobile number";
        }
        elseif(empty($fname) || empty($lname) || empty($mob))
        {
            echo "All fields are required";
        }
        else
        {
            $qcont = "INSERT INTO contacts (fname, lname, mob) VALUES (?, ?, ?)";
            $qcont = $conn->prepare($qcont);
            $qcont->bind_param("sss", $fname, $lname, $mob);
            $qcont->execute();
            $qcont->store_result();
            if($qcont->affected_rows == 1)
            {
                $cid = $qcont->insert_id;
                $quir = "INSERT INTO users_in_relation (user_id, contact_id, relation_id) VALUES(?, ?, ?)";
                $quir = $conn->prepare($quir);
                $quir->bind_param("ddd", $_SESSION["userid"], $cid, $rel);
                $quir->execute();
                $quir->store_result();
                if($quir->affected_rows == 1)
                {
                    echo "Contact added successfully";
                }
                else
                {
                    echo "Failed to add contact";
                }
            }
        }
    }

    if(isset($_POST["fname"]))
    {
        $fname = trim($_POST["fname"]);
        $lname = trim($_POST["lname"]);
        $res = trim($_POST["residence"]);
        $room = trim($_POST["room"]);
        $mob1 = trim($_POST["mob1"]);
        $mob2 = trim($_POST["mob2"]);
        $address = trim($_POST["address"]);

        $qubio = "UPDATE users SET fname = ?, lname = ?, residence = ?, room = ?, mob1 = ?, mob2 = ?, address = ? WHERE id = ?";
        $qubio = $conn->prepare($qubio);
        $qubio->bind_param("sssssssd", $fname, $lname, $res, $room, $mob1, $mob2, $address, $_SESSION["userid"]);
        $qubio->execute();
        $qubio->store_result();
        if($qubio->affected_rows > 0)
        {
            echo "Successfully updated";
        }
        else
        {
            echo "Failed. (Did you change anything?) Please refresh the page";
        }
    }
?>
