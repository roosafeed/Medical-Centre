<?php
    include_once("Includes/initiate.php");

    $med = "%". trim($_GET["term"]) ."%";
    $query = "SELECT M.id AS mid, MB.id AS id, M.name AS name, M.manufacturer AS man, MB.arr_date AS arr, MB.exp_date AS exp, MB.stock_num AS num FROM medicines M ";
    $query .= "INNER JOIN med_batch MB ON M.id = MB.med_id WHERE M.name LIKE ? AND DATE(CURDATE()) < DATE(MB.exp_date) ORDER BY MB.arr_date ASC LIMIT 6";
    $query = $conn->prepare($query);
    $query->bind_param("s", $med);
    $query->execute();
    $result = $query->get_result();
    $dataArray = array();

    while($row = $result->fetch_assoc())
    {
        $data["id"] = $row["mid"];
        $data["value"] = $row["id"] . ":" . $row["name"] . " (" . $row["man"] . ")";
        $data["desc"] = "Available:" . $row["num"] . "| Exp:" . $row["exp"] . "| Arrived:" . $row["arr"];
        $dataArray[] = $data;
    }

    $query->close();

    echo json_encode($dataArray);
?>
