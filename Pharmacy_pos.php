<?php
    $GLOBALS["page"] = "Pharmacy POS";
    include_once("Includes/header.php");
    if(!(isAdmin() || isHCstaff()))
    {
        header("Location: home.php");
    }

    include_once("Includes/header.php");
?>

<h3>POS</h3>
<div id="pos-left">
    <form method="post" action="" id="form-pos">
        <input name="pos-roll" type="text" id="pos-roll" placeholder="Rollnumber/ Employee ID" /><br />
        <input name="pos-user-id" type="hidden" id="pos-user-id" value="" />
        <div id="pos-meds"> 
            <!--
            <div id="pos-med-1" class="pos-med-class">           
                <input name="pos-med-batch-1" type="text" id="pos-med-batch-1" placeholder="Medicine Batch" />
                <input name="pos-batch-id-1" type="hidden" id="pos-batch-id-1" value="" />
                <input name="pos-med-num-1" type="number" id="pos-med-num-1" placeholder="Quantity" /><br />
                <input type="button" id="del-med-1" value="Remove" onclick="delMed_POS()"><br />
            </div>
            -->
        </div>
        <input type="button" value="Add Medicine" onclick="addMed_POS()"><br />
        <input name="submit-pos" type="submit" value="Checkout" />
        <input type="reset" value="Clear" />
    </form>
</div>

<div id="pos-right">
</div>

<script src="/JS/pos.js" type="text/javascript"></script>

<?php
    include_once("Includes/footer.php");
?>