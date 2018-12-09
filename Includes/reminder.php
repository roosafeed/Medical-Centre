<?php
    /*REMINDERS
        * Medicines that expire within 3 months (90 days)
        * Give the count of the medicines that are about to expire
        * Provide a link to see the complete list of medicines that are about to expire
    */

    $q = "SELECT COUNT(*) AS c ";
    $q .= "FROM medicines M INNER JOIN med_batch MB ON M.id = MB.med_id WHERE DATEDIFF(MB.exp_date, CURDATE()) < 91 AND ";
    $q .= "DATEDIFF(MB.exp_date, CURDATE()) > 0 ORDER BY DATEDIFF(MB.exp_date, CURDATE()) ASC";
    $q = $conn->query($q) or die("Error: " . $conn->error);
    $row = $q->fetch_assoc();
?>
<?php
    if(intval($row["c"]) > 0)
    {
        echo '<a href="/pharmacy.php#list-exp" id="link-reminder">';
        echo '<div id="cont-reminder">';
        echo '<h4>Number of batches that expire in 90 days: ' . $row["c"] . '</h4>';
        echo '</div></a>';
    }    
?>