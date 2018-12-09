<?php
    include_once("Includes/initiate.php");

    if(!(isAdmin() || isHCstaff()))
    {
        echo "Unauthorized"; //return nothing
    }

    else
    {
        //handle the incoming post request
        //type = med/stock/exp
        //med -> List all medicines
        //stock -> List all medicine stocks
        //exp -> List all stocks that expire in 3 months

        //Return JSON
        //name, man, arr, exp, num, tot, worth
        //exp (stock) -> Only date
        //exp (exp) -> Date + Remaining Days
        //tot -> Original cost of the stock
        //worth -> num * price per item

        //TODO: Pagination. Reduce memory usage. Expect large data

        if(isset($_POST["type"]))
        {
            $type = $_POST["type"];

            class stock
            {
                public $name = "";
                public $man = "";
                public $arr = "";
                public $exp = "";
                public $num = "";
                public $tot = "";
                public $worth = "";
            }

            class medicine
            {
                public $name = "";
                public $man = "";
            }

            class expire
            {
                public $name = "";
                public $man = "";
                public $arr = "";
                public $exp = "";
            }
            $output = array();
            //$med = new medicine();
          
            if($type == "med")
            {
                //List all medicines
                //name, man
                $q = "SELECT name, manufacturer AS man FROM medicines";
                $q = $conn->query($q) or die("Error: " . $conn->error);
                while($row = $q->fetch_assoc())
                {
                    $med = new medicine();
                    $med->name = $row["name"];
                    $med->man = $row["man"];
                    $output[] = $med;
                }

                echo json_encode($output);
            }

            elseif($type == "stock")
            {
                //List all medicine stocks
                //name, man, arr, exp, num, tot, worth
                $q = "SELECT M.name, M.manufacturer AS man, MB.exp_date AS exp, MB.arr_date AS arr, MB.stock_num AS num ";
                $q .= "FROM medicines M INNER JOIN med_batch MB ON M.id = MB.med_id";
                $q = $conn->query($q) or die("Error: " . $conn->error);
                while($row = $q->fetch_assoc())
                {
                    $batch = new stock();
                    $batch->name = $row["name"];
                    $batch->man = $row["man"];
                    $batch->arr = $row["arr"];
                    $batch->exp = $row["exp"];
                    $batch->num = $row["num"];
                    $output[] = $batch;
                }
                echo json_encode($output);
            }

            elseif($type == "exp")
            {
                //All stocks that expire in 3 months (90 days)
                //name, man, arr, exp
                $q = "SELECT M.name, M.manufacturer AS man, MB.arr_date AS arr, MB.exp_date AS exp, DATEDIFF(MB.exp_date, CURDATE()) AS dif ";
                $q .= "FROM medicines M INNER JOIN med_batch MB ON M.id = MB.med_id WHERE DATEDIFF(MB.exp_date, CURDATE()) < 701 AND ";
                $q .= "DATEDIFF(MB.exp_date, CURDATE()) > 0 ORDER BY DATEDIFF(MB.exp_date, CURDATE()) ASC";
                $q = $conn->query($q) or die("Error: " . $conn->error);
                while($row = $q->fetch_assoc())
                {
                    $med = new expire();
                    $med->name = $row["name"];
                    $med->man = $row["man"];
                    $med->arr = $row["arr"];
                    $med->exp = $row["exp"] . " (" . $row["dif"] . " Days)";
                    $output[] = $med;
                }
                echo json_encode($output);
            }
        }
    }
    
?>