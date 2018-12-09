<?php
    include_once("header.php");

    if(isAdmin() || isHCstaff())
    {
        header("Location: /home.php");
    }

    $qmeds = "SELECT M.name, M.manufacturer, P.* FROM prescriptions P INNER JOIN medical_records MR ON MR.id = P.mr_id ";
    $qmeds .= "INNER JOIN med_batch MB ON MB.id = P.mb_id INNER JOIN medicines M ON M.id = MB.med_id WHERE ";
    $qmeds .= "DATE(CURDATE()) <= DATE_ADD(DATE(MR.mrdate), INTERVAL (CEIL(P.number/(P.fn + P.an + P.nt))) DAY) AND MR.user_id = ?";
    $qmeds = $conn->prepare($qmeds);
    $qmeds->bind_param("d", $_SESSION["userid"]);
    $qmeds->execute();
    $med_res = $qmeds->get_result();
    $qmeds->close();

    $qmr = "SELECT U.idnum AS doc_idnum, U.fname AS doc_fname, U.lname AS doc_lname, MR.*, DATE_FORMAT(MR.mrdate, '%a, %e %b %Y | %r') AS date";
    $qmr .= " FROM medical_records MR INNER JOIN users U ON MR.doc_id = U.id INNER JOIN users U2 ON U2.id = MR.user_id WHERE U2.id = ? ORDER BY MR.mrdate DESC";
    $qmr = $conn->prepare($qmr);
    $qmr->bind_param("d", $_SESSION["userid"]);
    $qmr->execute();
    $mr_res = $qmr->get_result();
    $qmr->close();
    
?>

<div id="user-med-to-take">
    <h3>Medicine Reminder</h3>
    <?php
        if($med_res->num_rows == 0)
        {
            echo '<p>You are actively not under any medication from the Health Centre';
        }
        else
        {
            echo '<ul>';
            while($rows = $med_res->fetch_assoc())
            {
                echo '<li>';
                echo '<div><h4>' . $rows["name"] . " (" . $rows["manufacturer"] . ")</h4>";
                echo '<ul>';
                echo ($rows["af_food"] == 1)? ' <li>After Food</li> | ': ' <li>Before Food</li> | ';
                echo ($rows["fn"] == 1)? ' <li>Morning</li> ': '';
                echo ($rows["an"] == 1)? ' <li>Afternoon</li> ': '';
                echo ($rows["nt"] == 1)? ' <li>Night</li> ': '';
                echo '</ul></div></li>';
            }
            echo '</ul>';
        }
    ?>
</div>

<div id="user-mr-history">
    <h3>Medical Record History</h3>
    <?php
        if($mr_res->num_rows == 0)
        {
            echo '<p>You have no medical records yet. It will be added each time you visit the doctor at the NITC Health Centre.</p>';
        }
        else
        {
            $qmed = "SELECT P.*, M.name, M.manufacturer FROM prescriptions P INNER JOIN med_batch MB ON MB.id = P.mb_id ";
            $qmed .= "INNER JOIN medicines M ON M.id = MB.med_id WHERE P.mr_id = ?";
            $qmed = $conn->prepare($qmed);
            $qmed->bind_param("d", $mr_id);
            
            while($mr_row = $mr_res->fetch_assoc())
            {
                $mr_id = $mr_row["id"];
                $qmed->execute();
                $med_res = $qmed->get_result();

                echo '<div class="user-mr-result">';
                echo '<h3>'.$mr_row["date"].'</h3><h4>Dr. '.$mr_row["doc_fname"].' '.$mr_row["doc_lname"].' ('.$mr_row["doc_idnum"].')</h4>';
                echo '<p>'.$mr_row["doc_notes"].'</p><ul id="meds">';
                while($row = $med_res->fetch_assoc())
                {
                    echo '<li><div><h4>'.$row["name"].' ('.$row["manufacturer"].')</h4><ul>';
                    echo ($row["af_food"] == 1)? ' <li>After Food</li> | ': ' <li>Before Food</li> | ';
                    echo ($row["fn"] == 1)? ' <li>Morning</li> ': '';
                    echo ($row["an"] == 1)? ' <li>Afternoon</li> ': '';
                    echo ($row["nt"] == 1)? ' <li>Night</li> ': '';
                    echo '</ul></div></li>';
                } 
                echo '</ul></div>';
            }
        }
    ?>
</div>



