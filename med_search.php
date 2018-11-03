<?php
    include_once("Includes/initiate.php");

    $med = "%". trim($_GET["term"]) ."%";
    $query = "SELECT * FROM medicines WHERE name LIKE ? ORDER BY name ASC";
    $query = $conn->prepare($query);
    $query->bind_param("s", $med);
    $query->execute();
    $result = $query->get_result();
    $dataArray = array();

    while($row = $result->fetch_assoc())
    {
        $data["id"] = $row["id"];
        $data["value"] = $row["name"] . " (" . $row["manufacturer"] . ")";
        $dataArray[] = $data;
    }

    $query->close();

    echo json_encode($dataArray);
?>
