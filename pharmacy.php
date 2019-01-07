<?php
    /*
        *List of all the medicines
        *List of all the medicine stocks
        *Value of the remaining medicines
        *Medicine stocks that expire in 3 months
    */
    $GLOBALS["page"] = "Pharmacy";
    include_once("Includes/header.php");
    if(!(isAdmin() || isHCstaff()))
    {
        header("Location: home.php");
    }

    include_once("Includes/reminder.php");
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

    <div class="button" id="toggle-report">
        <h2>Report</h2>
    </div>
</div>

<div class="pharm-functions">
    <div class="function" id="list-med">
        <h3 class="admin-function-close">X</h3>        
        <h3>All Medicines</h3>
        <div id="cont-list-med">
            <table>
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Manufacturer</th>
                        <th title="Number of batches in use"># Batches</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="function" id="list-stock">
        <form id="stock-search-form" method="post">
            <input type="text" placeholder="Name of the medicine" name="stock-search-name" id="stock-search-name" />
            <input type="submit" name="stock-search" value="Search" />
            <input type="button" id="stock-search-close" value="Close Search" />
        </form>
        <h3 class="admin-function-close">X</h3>
        <h3>Stocks</h3>
        <div id="cont-list-stock">
            <table>
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Seller</th>
                        <th>Arrived</th>
                        <th>Expires On</th>
                        <th># Remaining</th>
                        <th>Total Cost</th>
                        <th>Worth</th>
                        <th>Modify</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6">Total worth</th>
                        <th id="stock-worth-sum"></th>
                        <th></th>
                    </tr>
                    <tr id="stock-more-tr">
                        <td colspan="2"><input type="button" id="stock-more" value="Load More" /></td>
                        <td colspan="6" id="stock-msg"></td>
                    </tr>
                </tfoot>
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
                        <th>Seller</th>
                        <th># Remaining</th>
                        <th>Arrived</th>
                        <th>Expires On</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="function" id="report">
        <h3 class="admin-function-close">X</h3>
        <h3>Report</h3>
        <p>Generate a report for the current month. <br />(From <?php echo date("01-F-Y") . " to " . date("d-F-Y"); ?>)</p>
        <a href="/month_report.php" target="_blank">
            <input type="button" value="Generate" />
        </a>
    </div>
</div>

<script src="/JS/pharmacy.js"></script>
<style>
    form
    {
        border: 0;
    }
    input[type=number]
    {
        width: 60px;
    }
</style>

<?php
    include_once("Includes/footer.php");
?>