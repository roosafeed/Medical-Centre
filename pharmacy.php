<?php
    /*
        *List of all the medicines
        *List of all the medicine stocks
        *Value of the remaining medicines
        *Medicine stocks that expire in 3 months
    */
    $GLOBALS["page"] = "Pharmacy";
    include_once("Includes/header.php");
?>
<h1>Pharmacy</h1>

<div id="admin-buttons">
    <div class="button" id="toggle-list-med">
        <h2>All Medicines</h2>
    </div>

    <div class="button" id="toggle-list-stock">
        <h2>All Stocks</h2>
    </div>

    <div class="button" id="toggle-list-exp">
        <h2>Expiring</h2>
    </div>
</div>

<div class="pharm-functions">
    <div class="function" id="list-med">
        <h3 class="admin-function-close">X</h3>        
        <h3>All Medicines</h3>
        <div id="cont-list-med">
            <table>
                <thead><tr><th>Medicine Name</th><th>Manufacturer</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="function" id="list-stock">
        <h3 class="admin-function-close">X</h3>
        <h3>Stocks</h3>
        <div id="cont-list-stock">
            <table>
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Manufacturer</th>
                        <th>Arrived</th>
                        <th>Expires On</th>
                        <th># Remaining</th>
                        <th>Total Cost</th>
                        <th>Worth</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
    </div>

    <div class="function" id="list-exp">
        <h3 class="admin-function-close">X</h3>
        <h3>Expiring Medicines</h3>
        <div id="cont-list-exp" class="flex-table">
            <table>
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Manufacturer</th>
                        <th>Arrived</th>
                        <th>Expires On</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="/JS/pharmacy.js"></script>

<?php
    include_once("Includes/footer.php");
?>