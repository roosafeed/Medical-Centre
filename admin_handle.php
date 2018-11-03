<?php
    include_once("Includes/initiate.php");

    if(isset($_POST["new-email"]))
    {
        $email = trim($_POST["new-email"]);
        $role = $_POST["new-role"];
        $fname = trim($_POST["new-fname"]);
        $lname = trim($_POST["new-lname"]);
        $idnum = trim($_POST["new-idnum"]);
        $res = trim($_POST["new-residence"]);
        $room = trim($_POST["new-room"]);
        $mob1 = trim($_POST["new-mob1"]);
        $mob2 = trim($_POST["new-mob2"]);
        $dob = trim($_POST["new-dob"]);
        $address = trim($_POST["new-address"]);
        $gender = trim($_POST["new-gender"]);
        
        $qcheck = "SELECT email FROM users WHERE LOWER(email) = LOWER(?) OR LOWER(idnum) = LOWER(?)";
        $qcheck = $conn->prepare($qcheck);
        $qcheck->bind_param("ss", $email, $idnum);
        $qcheck->execute();
        $qcheck->store_result();
        $nr = $qcheck->num_rows;
        $qcheck->close();

        $qcheck = "SELECT id FROM user_roles WHERE id = ?";
        $qcheck = $conn->prepare($qcheck);
        $qcheck->bind_param("d", $role);
        $qcheck->execute();
        $qcheck->store_result();
        $nr2 = $qcheck->num_rows;
        $qcheck->close();

        if($nr > 0)
        {
            echo "User exists.";
        }
        elseif($nr2 != 1)
        {
            echo "Invalid Role id";
        }
        elseif($nr == 0 && $nr2 == 1)
        {
            $query = "INSERT INTO users (email, fname, lname, password, idnum, residence, room, mob1, mob2, address, dob, gender) ";
            $query .= "VALUES (LOWER(?), ?, ?, SHA(UPPER(?)), UPPER(?), ?, ?, ?, ?, ?, ?, LOWER(?))";
            $query = $conn->prepare($query);
            $query->bind_param("ssssssssssss", $email, $fname, $lname, $idnum, $idnum, $res, $room, $mob1, $mob2, $address, $dob, $gender);
            $query->execute();
            $query->store_result();
            $ar = $query->affected_rows;
            $id = $query->insert_id;
            $query->close();
            if($ar == 1)
            {
                $qroles = "INSERT INTO users_in_roles (user_id, role_id) VALUES (?,?)";
                $qroles = $conn->prepare($qroles);
                $qroles->bind_param("dd", $id, $role);
                $qroles->execute();
                $qroles->store_result();
                $ar2 = $qroles->affected_rows;
                $qroles->close();
                if($ar2 != 1)
                {
                    echo "Unknown Error";
                }
                else
                {
                    echo "Successfully registered " . $email . " | " . $idnum;
                }
            }
            else
            {
                echo "Unknown Error.";
            }
        }
        
    }

    elseif(isset($_POST["med-name"]))
    {
        $med_name = trim($_POST["med-name"]);
        $med_man = trim($_POST["med-manufacturer"]);

        $qmchck = "SELECT name FROM medicines WHERE LOWER(name) = LOWER(?) AND LOWER(manufacturer) = LOWER(?)";
        $qmchck = $conn->prepare($qmchck);
        $qmchck->bind_param("ss", $med_name, $med_man);
        $qmchck->execute();
        $qmchck->store_result();
        $nr = $qmchck->num_rows;
        $qmchck->close();
        if($nr == 1)
        {
            echo "Medicine exists.";
        }
        else
        {
            $qmed = "INSERT INTO medicines (name, manufacturer) VALUES (?, ?)";
            $qmed = $conn->prepare($qmed);
            $qmed->bind_param("ss", $med_name, $med_man);
            $qmed->execute();
            $qmed->store_result();
            $ar = $qmed->affected_rows;
            $qmed->close();
            if($ar == 1)
            {
                echo "Medicine added successfully";
            }
            else
            {
                echo "Unknown Error";
            }
        }
    }

    elseif(isset($_POST["rel-name"]))
    {
        $rel = trim($_POST["rel-name"]);

        $qrchk = "SELECT relation FROM relations WHERE LOWER(relation) = LOWER(?)";
        $qrchk = $conn->prepare($qrchk);
        $qrchk->bind_param("s", $rel);
        $qrchk->execute();
        $qrchk->store_result();
        $nr = $qrchk->num_rows;
        $qrchk->close();

        if($nr == 1)
        {
            echo "Relation exists.";
        }
        else
        {
            $qrel = "INSERT INTO relations (relation) VALUES (UPPER(?))";
            $qrel = $conn->prepare($qrel);
            $qrel->bind_param("s", $rel);
            $qrel->execute();
            $qrel->store_result();
            $ar = $qrel->affected_rows;
            $qrel->close();

            if($ar == 1)
            {
                echo "Relation added | " . $rel;
            }
            else
            {
                echo "Unknown Error";
            }
        }
    }

    elseif(isset($_POST["reset-username"]))
    {
        $uname = trim($_POST["reset-username"]);
        $quchck = "SELECT idnum FROM users WHERE LOWER(email) = LOWER(?) OR LOWER(idnum) = LOWER(?)";
        $quchck = $conn->prepare($quchck);
        $quchck->bind_param("ss", $uname, $uname);
        $quchck->execute();
        $quchck->store_result();
        $quchck->bind_result($idnum);
        $quchck->fetch();
        $nr = $quchck->num_rows;
        $quchck->close();
        
        if($nr == 1)
        {
            $qrel = "UPDATE users SET password = SHA(UPPER(?)) WHERE LOWER(idnum) = LOWER(?)";
            $qrel = $conn->prepare($qrel);
            $qrel->bind_param("ss", $idnum, $idnum);
            $qrel->execute();
            $qrel->store_result();
            $ar = $qrel->affected_rows;
            $qrel->close();

            if($ar == 1)
            {
                echo "Password reset successfully to '" . strtoupper($idnum) . "'";
            }
            else
            {
                echo "Unknown Error or Password is '" . strtoupper($idnum) . "'";
            }
        }
        else
        {
            echo "Invalid user detail";
        }
    }

    elseif(isset($_POST["stock-med-id"]))
    {
        $med_id = trim($_POST["stock-med-id"]);
        $med_name = trim($_POST["stock-name"]);
        $mfg = $_POST["stock-mfg"];
        $exp = $_POST["stock-exp"];
        $arr = $_POST["stock-arr"];
        $num = $_POST["stock-num"];

        $name = explode("(", $med_name);
        $med_name = trim($name[0]);

        $mchck = "SELECT id FROM medicines WHERE id = ? AND LOWER(name) = LOWER(?)";
        $mchck = $conn->prepare($mchck);
        $mchck->bind_param("ds", $med_id, $med_name);
        $mchck->execute();
        $mchck->store_result();
        $nr = $mchck->num_rows;
        $mchck->close();

        if($nr == 1)
        {
            $qm = "INSERT INTO med_batch (mfg_date, exp_date, arr_date, stock_num, med_id) VALUES (?, ?, ?, ?, ?)";
            $qm = $conn->prepare($qm) or die($conn->error);
            $qm->bind_param("sssdd", $mfg, $exp, $arr, $num, $med_id) or die($qm->error);
            $qm->execute() or die($qm->error);
            $qm->store_result() or die($qm->error);
            $ar = $qm->affected_rows;
            $qm->close();
            if($ar == 1)
            {
                echo "Stock added successfully";
            }
            else
            {
                echo "Unknown Error";
            }
        }
        else
        {
            echo "Medicine does not exits";
        }
    }

    elseif(isset($_POST["mr-user"]))
    {
        $uid = $_POST["mr-user-id"];
        $did = $_POST["mr-dr-id"];
        $notes = trim($_POST["mr-notes"]);
        
        $q = "INSERT INTO medical_records (doc_notes, mrdate, doc_id, user_id) VALUES (?, NOW(), ?, ?)";
        $q = $conn->prepare($q);
        $q->bind_param("sdd", $notes, $did, $uid);
        $q->execute();
        //$q->store_result();
        $id = $q->insert_id;
        $ar = $q->affected_rows;
        //$q->close();

        if($ar == 1)
        {
            $numb = 0; $af_f = 0; $fn = 0; $an = 0; $nt = 0; $intn = 0; $med_name = "";

            $q2 = "INSERT INTO prescriptions (mr_id, mb_id, number, af_food, fn, an, nt) ";
            $q2 .= "SELECT ?, MB.id, ?, ?, ?, ?, ? FROM med_batch MB INNER JOIN medicines M ON M.id = MB.med_id ";
            $q2 .= "WHERE M.name = ? AND DATE(CURDATE()) < DATE_SUB(DATE(MB.exp_date), INTERVAL ? DAY) ";
            $q2 .= "AND MB.stock_num > ? ORDER BY MB.arr_date LIMIT 1";
            $q2 = $conn->prepare($q2) or die($conn->error);
            $q2->bind_param("diiiiisii", $id, $numb, $af_f, $fn, $an, $nt, $med_name, $intn, $numb) or die($q->error);
            

            $n = 1;
            while(isset($_POST["mr-med" . $n]))
            {
                $intn = 0;
                $med_name = $_POST["mr-med" . $n];
                $numb = $_POST["mr-med-num" . $n];
                $af_f = (isset($_POST["mr-af" . $n]))? 1 : 0;
                $fn = (isset($_POST["mr-fn" . $n]))? 1 : 0;
                $an = (isset($_POST["mr-an" . $n]))? 1 : 0;
                $nt = (isset($_POST["mr-nt" . $n]))? 1 : 0;
                $intn = $numb / ($fn + $an + $nt);
                $intn = ceil($intn);

                $q2->execute() or die($q2->error);
                $n = $n + 1;
            }
            $r = $q2->store_result();
            
            if($q2->affected_rows > 0)
            {
                echo "Successfully Added";
            }
            else
            {
                echo "Error";
            }

            $q2->close();
        }
        else
        {
            echo "Unknown Error";
        }
    }
?>
