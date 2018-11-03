<?php
    include_once("Includes/initiate.php");

    $role = trim($_GET["role"]);
    $term = "%" . trim($_GET["term"]) . "%";

    $query = "";

    if($role == "user")
    {
        $query = "SELECT id, fname, lname, idnum FROM users WHERE idnum LIKE ? OR email LIKE ?";
        $query = $conn->prepare($query);
        $query->bind_param("ss", $term, $term);
    }
    elseif($role == "dr")
    {
        $query = "SELECT U.id AS id, U.fname AS fname, U.lname AS lname, U.idnum AS idnum FROM users U INNER JOIN users_in_roles R ON R.user_id = U.id";
        $query .= " WHERE (U.idnum LIKE ? OR U.fname LIKE ? OR U.lname LIKE ?) AND R.role_id = 2";
        $query = $conn->prepare($query);
        $query->bind_param("sss", $term, $term, $term);
    }  
    $query->execute();
    $result = $query->get_result();
    $dataArray = array();

    while($row = $result->fetch_assoc())
    {
        $data["id"] = $row["id"];
        $data["value"] = $row["fname"] . " " . $row["lname"] . " (" . $row["idnum"] . ")";
        $dataArray[] = $data;
    }

    $query->close();

    echo json_encode($dataArray);
?>
