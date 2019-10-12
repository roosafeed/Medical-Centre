<?php
    include_once("Includes/initiate.php");

    $med = "%". trim($_GET["term"]) ."%";
    $type = trim($_GET["type"]);

    if($type == "new-med")
    {
        $query = "SELECT M.id AS mid, M.name AS name, M.manufacturer AS man FROM medicines M WHERE M.name LIKE ?";
    }
    elseif($type == "stock-med")
    {
        $query = "SELECT M.id AS mid, MB.id AS id, M.name AS name, M.manufacturer AS man, DATE_FORMAT(MB.arr_date, '%d-%m-%Y') AS arr, ";
        $query .= "DATE_FORMAT(MB.exp_date, '%d-%m-%Y') AS exp, IFNULL(MB.init_stock - MT.dif, MB.init_stock) AS num FROM medicines M ";
        $query .= "INNER JOIN med_batch MB ON M.id = MB.med_id ";
        $query .= "LEFT JOIN (SELECT IFNULL(SUM(num), 0) AS dif, batch_id FROM med_transaction GROUP BY batch_id) MT ON MT.batch_id = MB.id ";
        $query .= "WHERE M.name LIKE ? AND DATE(CURDATE()) < DATE(MB.exp_date) AND IFNULL(MB.init_stock - MT.dif, MB.init_stock) > 0 ORDER BY MB.arr_date ASC LIMIT 6";
    }

    $query = $conn->prepare($query);
    $query->bind_param("s", $med);
    $query->execute();
    $result = $query->get_result();
    $dataArray = array();

    if($type == "stock-med")
    {
        while($row = $result->fetch_assoc())
        {
            $data["id"] = $row["mid"];
            $data["bid"] = $row["id"];
            $data["value_bid"] = $row["id"] . ":" . $row["name"] . " (" . $row["man"] . ")";
            $data["value"] = $row["name"] . " (" . $row["man"] . ")";
            $data["desc"] = "Available:<b>" . $row["num"] . "</b>| Exp:" . $row["exp"] . "| Arr:" . $row["arr"];
            $dataArray[] = $data;
        }  
    }

    elseif($type == "new-med")
    {
        while($row = $result->fetch_assoc())
        {
            $data["id"] = $row["mid"];
            $data["value"] = $row["name"];
            $data["desc"] = "Manufacturer: " . $row["man"];
            $dataArray[] = $data;
        }
    }
    

    $query->close();

    echo json_encode($dataArray);
?>
