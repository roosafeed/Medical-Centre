<?php
    include_once("Includes/initiate.php");

    class stock
    {
        public $id = 0;
        public $name = "";
        public $man = "";
        public $arr = "";
        public $exp = "";
        public $num = "";
        public $price = "";
        public $seller = "";
        public $tot = "";
        public $worth = "";
    }

    class medicine
    {
        public $name = "";
        public $man = "";
        public $bnum = "";
    }

    class expire
    {
        public $name = "";
        public $man = "";
        public $arr = "";
        public $exp = "";
        public $num = "";
        public $tot = "";
        public $seller = "";
    }

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
        //tot -> Initial stock number
        //price -> Original cost of the stock
        //worth -> num * price per item
        //bnum -> number of batches of a medicine that has not expired

        //TODO: Pagination. Reduce memory usage. Expect large data

        if(isset($_POST["type"]))
        {
            $type = $_POST["type"];

            
            $output = array();
            //$med = new medicine();
          
            if($type == "med")
            {
                //List all medicines
                //name, man
                $q = "SELECT M.name, M.manufacturer AS man, COUNT(MB.id) AS bnum FROM medicines M INNER JOIN med_batch MB ON M.id = MB.med_id ";
                $q .= "WHERE DATEDIFF(MB.exp_date, CURDATE()) > 0 GROUP BY M.id";
                $q = $conn->query($q) or die("Error: " . $conn->error);
                while($row = $q->fetch_assoc())
                {
                    $med = new medicine();
                    $med->name = $row["name"];
                    $med->man = $row["man"];
                    $med->bnum = $row["bnum"];
                    $output[] = $med;
                }

                echo json_encode($output);
            }

            elseif($type == "stock")
            {
                //List all medicine stocks
                //name, man, arr, exp, num, tot, worth
                $q = 'SELECT MB.id, M.name, M.manufacturer AS man, MB.init_stock, MB.price, DATE_FORMAT(MB.exp_date, "%d-%m-%Y") AS exp, MB.seller, ';
                $q .= 'DATE_FORMAT(MB.arr_date, "%d-%m-%Y") AS arr, MB.stock_num AS num FROM medicines M INNER JOIN med_batch MB ON M.id = MB.med_id ';
                $q .= "WHERE DATEDIFF(MB.exp_date, CURDATE()) > 0 ORDER BY MB.entry DESC, MB.arr_date DESC, (MB.init_stock - MB.stock_num) ASC ";
                $q .= "LIMIT 1000";
                $q = $conn->query($q) or die("Error: " . $conn->error);
                while($row = $q->fetch_assoc())
                {
                    $batch = new stock();
                    $batch->id = $row["id"];
                    $batch->name = $row["name"];
                    $batch->man = $row["man"];
                    $batch->arr = $row["arr"];
                    $batch->exp = $row["exp"];
                    $batch->num = $row["num"];
                    $batch->tot = $row["init_stock"];
                    $batch->price = $row["price"];
                    $batch->seller = $row["seller"];
                    $batch->worth = round(floatval(($row["price"] / $row["init_stock"]) * $row["num"]), 2);
                    $output[] = $batch;
                }
                echo json_encode($output);
            }

            elseif($type == "exp")
            {
                //All stocks that expire in 3 months (90 days)
                //name, man, arr, exp
                $q = 'SELECT M.name, M.manufacturer AS man, DATE_FORMAT(MB.arr_date, "%d-%m-%Y") AS arr, DATE_FORMAT(MB.exp_date, "%d-%m-%Y") AS exp, MB.seller, ';
                $q .= "DATEDIFF(MB.exp_date, CURDATE()) AS dif, MB.stock_num, MB.init_stock FROM medicines M INNER JOIN med_batch MB ON M.id = MB.med_id WHERE ";
                $q .= "DATEDIFF(MB.exp_date, CURDATE()) < 91 AND DATEDIFF(MB.exp_date, CURDATE()) > 0 ORDER BY DATEDIFF(MB.exp_date, CURDATE()) ASC";
                $q = $conn->query($q) or die("Error: " . $conn->error);
                while($row = $q->fetch_assoc())
                {
                    $med = new expire();
                    $med->name = $row["name"];
                    $med->man = $row["man"];
                    $med->arr = $row["arr"];
                    $med->exp = $row["exp"] . " (" . $row["dif"] . " Days)";
                    $med->num = $row["stock_num"];
                    $med->tot = $row["init_stock"];
                    $med->seller = $row["seller"];
                    $output[] = $med;
                }
                echo json_encode($output);
            }
        }

        elseif(isset($_POST["stock-mod-num"]))
        {
            //Reduce stock-mod-num from the stock
            $num = trim($_POST["stock-mod-num"]);
            $id = $_POST["stock-mod-id"];
            $q = "UPDATE med_batch SET stock_num = (stock_num - ?) WHERE id = ?";
            $q = $conn->prepare($q) or die($conn->error);
            $q->bind_param("dd", $num, $id);
            $q->execute() or die($q->error);
            $q->store_result();
            if($q->affected_rows == 1)
            {
                echo "200"; 
            }
            else
            {
                echo "error";
            }
            $q->close();
        }

        elseif(isset($_POST["stock-search-term"]))
        {
            $term = trim($_POST["stock-search-term"]);
            $term = "%" . $term . "%";
            $output = array();

            $q = 'SELECT MB.id, M.name, M.manufacturer AS man, MB.init_stock, MB.price, DATE_FORMAT(MB.exp_date, "%d-%m-%Y") AS exp, MB.seller, ';
            $q .= 'DATE_FORMAT(MB.arr_date, "%d-%m-%Y") AS arr, MB.stock_num AS num FROM medicines M INNER JOIN med_batch MB ON M.id = MB.med_id ';
            $q .= "WHERE DATEDIFF(MB.exp_date, CURDATE()) > 0 AND M.name LIKE ? ";
            $q .= "ORDER BY MB.entry DESC, MB.arr_date DESC, (MB.init_stock - MB.stock_num) ASC";
            
            $q = $conn->prepare($q) or die($conn->error);
            $q->bind_param("s", $term);
            $q->execute() or die($q->error);
            $q = $q->get_result();

            while($row = $q->fetch_assoc())
            {
                $batch = new stock();
                $batch->id = $row["id"];
                $batch->name = $row["name"];
                $batch->man = $row["man"];
                $batch->arr = $row["arr"];
                $batch->exp = $row["exp"];
                $batch->num = $row["num"];
                $batch->tot = $row["init_stock"];
                $batch->price = $row["price"];
                $batch->seller = $row["seller"];
                $batch->worth = round(floatval(($row["price"] / $row["init_stock"]) * $row["num"]), 2);
                $output[] = $batch;
            }
            echo json_encode($output);
        }

        elseif(isset($_POST["more"]))
        {
            if($_POST["more"] == "stock")
            {
                $output = array();
                $q = 'SELECT MB.id, M.name, M.manufacturer AS man, MB.init_stock, MB.price, DATE_FORMAT(MB.exp_date, "%d-%m-%Y") AS exp, MB.seller, ';
                $q .= 'DATE_FORMAT(MB.arr_date, "%d-%m-%Y") AS arr, MB.stock_num AS num FROM medicines M INNER JOIN med_batch MB ON M.id = MB.med_id ';
                $q .= "WHERE DATEDIFF(MB.exp_date, CURDATE()) > 0 AND (MB.id < ? OR MB.id > ?) ";
                $q .= "ORDER BY MB.entry DESC, MB.arr_date DESC, (MB.init_stock - MB.stock_num) ASC LIMIT 1000";

                $q = $conn->prepare($q) or die($conn->error);
                $q->bind_param("dd", $_POST["min-id"], $_POST["max-id"]);
                $q->execute() or die($q->error);
                $q = $q->get_result();

                while($row = $q->fetch_assoc())
                {
                    $batch = new stock();
                    $batch->id = $row["id"];
                    $batch->name = $row["name"];
                    $batch->man = $row["man"];
                    $batch->arr = $row["arr"];
                    $batch->exp = $row["exp"];
                    $batch->num = $row["num"];
                    $batch->tot = $row["init_stock"];
                    $batch->price = $row["price"];
                    $batch->seller = $row["seller"];
                    $batch->worth = round(floatval(($row["price"] / $row["init_stock"]) * $row["num"]), 2);
                    $output[] = $batch;
                }
                echo json_encode($output);
            }
        }
    }
    
?>