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

    class transaction
    {
        public $id;
        public $medname;
        public $man;
        public $buyer;
        public $bid;
        public $vendor;
        public $vid;
        public $date;
        public $more;
        public $num;
    }

    if(!(isAdmin() || isHCstaff() || isViewOnly()))
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
                $q = "SELECT M.name, M.manufacturer AS man, IFNULL(COUNT(MB.id), 0) AS bnum FROM medicines M LEFT JOIN ";
                $q .= "(SELECT med_id, exp_date, id FROM med_batch WHERE DATEDIFF(exp_date, CURDATE()) > 0) MB ON M.id = MB.med_id ";
                $q .= "GROUP BY M.id";
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
                $q ='SELECT MB.id, M.name, M.manufacturer AS man, MB.init_stock, MB.price, DATE_FORMAT(MB.exp_date, "%d-%m-%Y") AS exp, MB.seller, ';
                $q .= 'DATE_FORMAT(MB.arr_date, "%d-%m-%Y") AS arr, IFNULL(MB.init_stock - MT.dif, MB.init_stock) AS num ';
                $q .= "FROM medicines M INNER JOIN med_batch MB ON M.id = MB.med_id ";
                $q .= "LEFT JOIN (SELECT IFNULL(SUM(num), 0) AS dif, batch_id FROM med_transaction GROUP BY batch_id) MT ON MT.batch_id = MB.id ";
                $q .= "WHERE DATEDIFF(MB.exp_date, CURDATE()) > 0 ORDER BY M.name ASC, MB.entry DESC, MB.arr_date DESC ";
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
                $q .= "DATEDIFF(MB.exp_date, CURDATE()) AS dte, IFNULL(MB.init_stock - MT.dif, MB.init_stock) AS num, MB.init_stock FROM medicines M ";
                $q .= "INNER JOIN med_batch MB ON M.id = MB.med_id ";
                $q .= "LEFT JOIN (SELECT IFNULL(SUM(num), 0) AS dif, batch_id FROM med_transaction GROUP BY batch_id) MT ON MT.batch_id = MB.id WHERE ";
                $q .= "DATEDIFF(MB.exp_date, CURDATE()) < 91 AND DATEDIFF(MB.exp_date, CURDATE()) > 0 ORDER BY DATEDIFF(MB.exp_date, CURDATE()) ASC";
                $q = $conn->query($q) or die("Error: " . $conn->error);
                while($row = $q->fetch_assoc())
                {
                    $med = new expire();
                    $med->name = $row["name"];
                    $med->man = $row["man"];
                    $med->arr = $row["arr"];
                    $med->exp = $row["exp"] . " (" . $row["dte"] . " Days)";
                    $med->num = $row["num"];
                    $med->tot = $row["init_stock"];
                    $med->seller = $row["seller"];
                    $output[] = $med;
                }
                echo json_encode($output);
            }
        }

        elseif(isset($_POST["stock-mod-num"]))
        {
            if(isViewOnly())
            {
                echo "Unauthorized";
            }
            else
            {
                //Reduce stock-mod-num from the stock -> deprecated
                //Update: add to med_transaction
                $num = trim($_POST["stock-mod-num"]);
                $id = $_POST["stock-mod-id"];
                //$q = "UPDATE med_batch SET stock_num = (stock_num - ?) WHERE id = ?";
                $q2 = "INSERT INTO med_transaction (batch_id, num, tr_date) VALUES (?, ?, NOW())";
                $q2 = $conn->prepare($q2) or die($conn->error);
                //$q = $conn->prepare($q) or die($conn->error);
                //$q->bind_param("dd", $num, $id);
                $q2->bind_param("dd", $id, $num);
                //$q->execute() or die($q->error);
                //$q->store_result();
                $q2->execute() or die($q2->error);
                $q2->store_result();
                $ar = $q2->affected_rows;
                $id = $q2->insert_id;
                $q2->close();
                if($ar == 1)
                {
                    $q3 = "INSERT INTO med_vendor (mt_id, vid, mt_date) VALUES (?, ?, NOW())";
                    $q3 = $conn->prepare($q3) or die($conn->error);
                    $q3->bind_param("dd", $id, $_SESSION["userid"]);
                    $q3->execute() or die($q3->error);
                    $q3->store_result();
                    if($q3->affected_rows == 1)
                    {
                        echo "200"; 
                    }
                    else
                    {
                        echo "error";
                    }
                    $q3->close();
                }
                else
                {
                    echo "error";
                }
            
                //$q->close();
            }
            
        }

        elseif(isset($_POST["stock-search-term"]))
        {
            $term = trim($_POST["stock-search-term"]);
            $term = "%" . $term . "%";
            $output = array();

            $q = 'SELECT MB.id, M.name, M.manufacturer AS man, MB.init_stock, MB.price, DATE_FORMAT(MB.exp_date, "%d-%m-%Y") AS exp, MB.seller, ';
            $q .= 'DATE_FORMAT(MB.arr_date, "%d-%m-%Y") AS arr, IFNULL(MB.init_stock - MT.dif, MB.init_stock) AS num FROM medicines M ';
            $q .= "INNER JOIN med_batch MB ON M.id = MB.med_id ";
            $q .= "LEFT JOIN (SELECT IFNULL(SUM(num), 0) AS dif, batch_id FROM med_transaction GROUP BY batch_id) MT ON MT.batch_id = MB.id ";
            $q .= "WHERE DATEDIFF(MB.exp_date, CURDATE()) > 0 AND M.name LIKE ? ";
            $q .= "ORDER BY M.name ASC, MB.entry DESC, MB.arr_date DESC";
            
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
                $q .= 'DATE_FORMAT(MB.arr_date, "%d-%m-%Y") AS arr, IFNULL(MB.init_stock - MT.dif, MB.init_stock) AS num FROM medicines M ';
                $q .= "INNER JOIN med_batch MB ON M.id = MB.med_id ";
                $q .= "LEFT JOIN (SELECT IFNULL(SUM(num), 0) AS dif, batch_id FROM med_transaction GROUP BY batch_id) MT ON MT.batch_id = MB.id ";
                $q .= "WHERE DATEDIFF(MB.exp_date, CURDATE()) > 0 AND (MB.id < ? OR MB.id > ?) ";
                $q .= "ORDER BY MB.entry DESC, MB.arr_date DESC LIMIT 1000";

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

        elseif(isset($_POST["record"]))
        {
            $check_id = 0;
            if(isset($_POST["max"]))
            {
                $check_id = $_POST["max"];
            }
            if(isset($_POST["min"]))
            {
                $check_id = $_POST["min"];
            }
            
            $output = array();
            $q = "SELECT MT.id, MT.num, IFNULL(DATE_FORMAT(P.pos_date, '%r %d-%m-%Y'), DATE_FORMAT(MV.mt_date, '%r %d-%m-%Y')) AS date, ";
            $q .= "IFNULL(U.fname, '') AS ufname, IFNULL(U.lname, '') AS ulname, IFNULL(U.idnum, '') AS uid, M.name, M.manufacturer, ";
            $q .= "IFNULL(V.fname, V2.fname) AS vfname, IFNULL(V.lname, V2.lname) AS vlname, IFNULL(V.idnum, V2.idnum) AS vid ";
            $q .= "FROM med_transaction MT INNER JOIN med_batch MB ON MB.id = MT.batch_id INNER JOIN medicines M ON M.id = MB.med_id ";
            $q .= "LEFT JOIN pos_transaction PT ON PT.trans_id = MT.id LEFT JOIN pos P ON P.id = PT.pos_id ";
            $q .= "LEFT JOIN users U ON U.id = P.buyer_id LEFT JOIN users V ON V.id = P.vendor_id ";
            $q .= "LEFT JOIN med_vendor MV ON MV.mt_id = MT.id ";
            $q .= "LEFT JOIN users V2 ON V2.id = MV.vid ";
            if(isset($_POST["max"]))
            {
                $q .= "WHERE MT.id > ? ORDER BY MT.tr_date DESC LIMIT 1000";
            }
            if(isset($_POST["min"]))
            {
                $q .= "WHERE MT.id < ? ORDER BY MT.tr_date DESC LIMIT 1000";
            }
            

            $q = $conn->prepare($q) or die($conn->error);
            $q->bind_param("d", $check_id);
            $q->execute() or die($q->error);
            $q = $q->get_result();

            while($row = $q->fetch_assoc())
            {
                $trans = new transaction();
                $trans->id = $row["id"];
                $trans->medname = $row["name"];
                $trans->man = $row["manufacturer"];
                $trans->vendor = $row["vfname"] . " " . $row["vlname"];
                $trans->buyer = $row["ufname"] . " " . $row["ulname"];
                $trans->bid = $row["uid"];
                $trans->vid = $row["vid"];
                $trans->date = $row["date"];
                $trans->num = $row["num"];
                if($row["ufname"] == "")
                {
                    $trans->more = "";
                }
                else
                {
                    $trans->more = "POS";
                }
                $output[] = $trans;
            }

            echo json_encode($output);
        }
    }
    
?>