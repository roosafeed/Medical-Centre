<?php
    include_once("Includes/initiate.php");
    require('Includes/fpdf.php');   //http://www.fpdf.org/en/doc/index.php

    if(!(isAdmin() || isHCstaff()))
    {
        header("Location: home.php");
    }

    /*
    "SELECT M.name, SUM(MB.init_stock) AS tot, (SUM(MB.init_stock) - IFNULL(SUM(MT.num), 0)) AS stock, MB."
    FROM medicines M
    LEFT JOIN med_batch MB 
     ON M.id = Mb.med_id
    LEFT JOIN med_transaction MT
     ON MB.id = MT.batch_id 
    WHERE MONTH(IFNULL(MT.tr_date, NOW())) = MONTH(NOW()) 
    AND DATEDIFF(MB.exp_date, CURDATE()) > 0
    GROUP BY M.id 
    */

    $title = "Report for the month of " . date("F Y") . " (upto " . date("d-m-Y") . ")";

    $q = "SELECT M.name AS name, IFNULL(SUM(MB.init_stock), 0) AS tot, IFNULL((SUM(MB.init_stock) - IFNULL(SUM(M2.s), 0)), 0) AS stock, ";
    $q .= "SUM(IFNULL(MB.price, 0)) - SUM(IFNULL(M2.w, 0)) AS worth, MB.price FROM medicines M LEFT JOIN (SELECT * FROM med_batch ";
    $q .= "WHERE DATE(exp_date) >= DATE(NOW())) MB ON M.id = MB.med_id LEFT JOIN ";
    $q .= "(SELECT MB2.id AS id2, SUM(MT2.num) AS s, price, (MB2.price/MB2.init_stock) * SUM(MT2.num) AS w FROM med_transaction MT2 ";
    $q .= "INNER JOIN (SELECT * FROM med_batch WHERE DATE(exp_date) >= DATE(NOW())) MB2 ON MB2.id = MT2.batch_id GROUP BY MB2.id) M2 ";
    $q .= "ON M2.id2 = MB.id GROUP BY M.name ORDER BY M.name";

    $q = $conn->query($q) or die("Error CNQ1");
    
    $width = array(10, 120, 25, 30);         //Column widths
    $pdf = new FPDF();
    $pdf->SetTitle("Monthly Report_" . date("F_Y") . "_" . date("d-m-Y"));
    $pdf->SetCreator("NITC Health Centre");
    $pdf->SetSubject($title);
    $pdf->SetAuthor($_SESSION["fname"]);

    $pdf->AddPage();

    $pdf->SetFont("Arial", "B", 18);
    $pdf->Cell(0, 8, "NITC Health Centre", 0, 1, "C");

    $pdf->SetFont("Arial", "", 14);
    $pdf->Cell(0, 8, $title, 0, 1);

    $pdf->SetFont("Arial", "B", 16);    //Bold 16pt - Headers   
    $pdf->Cell($width[0], 8, "#", 1, 0, "C"); 
    $pdf->Cell($width[1], 8, "Medicine", 1, 0, "C");   //w, h, txt, border, next ln(0,1,2), align
    $pdf->Cell($width[2], 8, "Stock", 1, 0, "C");
    $pdf->Cell($width[3], 8, "Worth", 1, 0, "C");
    $pdf->Ln();

    $pdf->SetFont("Arial", "", 12);
    $i = 1;
    while($r = $q->fetch_assoc())
    {
        $l = $pdf->GetStringWidth($r["name"]);
        if($l >= $width[1])
        {
            $r["name"] = substr($r["name"], 0, intval($width[1] - $l - 5)) . "...";
        }        

        $pdf->Cell($width[0], 6, $i, 1);
        $pdf->Cell($width[1], 6, $r["name"], 1);
        $pdf->Cell($width[2], 6, $r["stock"], 1);
        $pdf->Cell($width[3], 6, round(floatval($r["worth"]), 2), 1);
        $pdf->Ln();
        $i = $i + 1;
    }

    $pdf->Output("I", "Monthly_Report_" . date("F_Y") . ".pdf");
?>
