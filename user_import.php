<?php
    $GLOBALS["page"] = "Import Users";
    include_once("Includes/header.php");
    if(!(isAdmin() || isHCstaff()))
    {
        header("Location: home.php");
    }
?>
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
<script src="/JS/user_import.js"></script>

<div id="import_user" style="display: none;">
    <h3>Import Excel sheet</h3>
    <input id="xlfile" name="xlfile" type="file" />
</div>
<br />

<div id="import_result" style="display: none;">
    <table>
        <thead>            
            <tr>
                <th>Type</th>
                <th>First Name</th>
                <th>Surname</th>
                <th>Email</th>
                <th>ID</th>
                <th>Residence</th>
                <th>Room #</th>
                <th>Mobile #</th>
                <th>Mobile2 #</th>
                <th>DoB</th>
                <th>Gender</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <td>
                    
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<br /><br /><br />

<div id="import_footer">
    <input style="display:none;" type="button" value="Submit" id="import_submit" />
    <p id="sheet-error-msg" style="color: #f00;"></p>
</div>

<?php
    include_once("Includes/footer.php");
?>