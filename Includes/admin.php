<?php
    include_once("header.php");

    if(!(isAdmin() || isHCstaff()))
    {
        header("Location: /home.php");
    }

    include_once("reminder.php");
    
?>

<div id="admin-buttons">
    <div class="button" id="toggle-create-user">
        <h2>Create User</h2>
    </div>
    <div class="button" id="toggle-add-mr">
        <h2>Add Medical Record</h2>
    </div>
    <div class="button" id="toggle-del-last-record">
        <h2>Delete Last Record</h2>
    </div>
    <div class="button" id="toggle-add-med-stock">
        <h2>Add Medicine Stock</h2>
    </div>
    <div class="button" id="toggle-add-med">
        <h2>Add New Medicine</h2>
    </div>
    <div class="button" id="toggle-get-user-history">
        <h2>Get User History</h2>
    </div>
    <div class="button" id="toggle-reset-user-pass">
        <h2>Reset User Password</h2>
    </div>
    <div class="button" id="toggle-add-relation">
        <h2>Add New Relation</h2>
    </div>
    <div class="button" id="toggle-get-contact">
        <h2>Get User Contacts</h2>
    </div>
    <div class="button" id="toggle-get-user-details">
        <h2>Get User Details</h2>
    </div>
    
</div>


<div class="admin-functions">
    <div class="function" id="create-user">
        <form id="create-user-form" name="create-user-form" action="" method="post">
            <h3 class="admin-function-close">X</h3>
            <h3>Create New User</h3>
            <select name="new-role" id="new-role">            
            <?php
                $qroles = "SELECT * FROM user_roles";
                $roles = $conn->query($qroles);
                while($rows = $roles->fetch_assoc())
                {
                    echo '<option value="' . $rows["id"] . '">' . $rows["role"] . '</option>';
                }
                $roles->close();
            ?>
            </select>
            <input type="text" name="new-email" id="new-email" placeholder="Email" title="Email" /> <br />
            <input type="text" name="new-fname" id="new-fname" placeholder="First Name" title="First Name" /> <br />
            <input type="text" name="new-lname" id="new-lname" placeholder="Last Name" title="Last Name" /> <br />
            <input type="text" name="new-idnum" id="new-idnum" placeholder="Roll Number/ Employee id" title="Roll Number/ Employee id" /> <br />
            <input type="text" name="new-residence" id="new-residence" placeholder="Hostel/ Residence" title="Hostel/ Residence" /> <br />
            <input type="text" name="new-room" id="new-room" placeholder="Hostel room number" title="Hostel room number" /> <br />
            <input type="text" name="new-mob1" id="new-mob1" placeholder="Mobile Number" title="Mobile Number" /> <br />
            <input type="text" name="new-mob2" id="new-mob2" placeholder="Mobile Number 2" title="Mobile Number 2" /> <br />
            <input type="date" name="new-dob" id="new-dob" placeholder="" title="Date of birth" /> <br />
            <textarea name="new-address" id="new-address" placeholder="Home address" title="Home address"></textarea> <br />
            <input type="radio" name="new-gender" id="new-gender" value="male" title="Gender" /> Male <br />
            <input type="radio" name="new-gender" id="new-gender" value="female" title="Gender" /> Female <br />
            <input type="radio" name="new-gender" id="new-gender" value="other" title="Gender" /> Other <br />

            <input type="submit" name="new-register" value="Register" /> 
            <input type="reset" value="Reset" /> <br />
        </form>
    </div>

    <div class="function" id="add-mr">
        <form id="add-mr-form" method="post" action="">
            <h3 class="admin-function-close">X</h3>
            <h3>Create Medical Record</h3>
            <input type="text" name="mr-user" id="mr-user" placeholder="Benficiary Roll Number/ Employee ID/ Email" title="Benficiary Roll Number/ Employee ID/ Email" />
            <input type="hidden" name="mr-user-id" id="mr-user-id" value="" /><br />
            <input type="text" name="mr-dr" id="mr-dr" placeholder="Doctor's Name/ Employee ID" title="Doctor's Name/ Employee ID" />
            <input type="hidden" name="mr-dr-id" id="mr-dr-id" value="" /><br />
            <textarea name="mr-notes" id="mr-notes"></textarea><br />
            <div id="mr-meds">
                <input type="text" name="mr-med1" class="mr-meds-input" placeholder="Medicine" />
                <input type="number" name="mr-med-num1" class="input-small" placeholder="Number" />
                <label for="mr-af1">After Food</label>
                <input type="checkbox" name="mr-af1" id="mr-af1" class="med-time" />
                <label for="mr-fn1">Forenoon</label>
                <input type="checkbox" class="med-time" name="mr-fn1" id="mr-fn1" />
                <label for="mr-an1">Afternoon</label>
                <input type="checkbox" class="med-time" name="mr-an1" id="mr-an1" />
                <label for="mr-nt1">Night</label>
                <input type="checkbox" class="med-time" name="mr-nt1" id="mr-nt1" />
                <br />
            </div>
            <input type="button" id="mr-add-med" onclick="addMed()" value="Add" /><br />
            <input type="submit" name="mr-sub" value="Add Record" />
            <input type="reset" value="Reset" />
        </form>
    </div>

    <div class="function" id="add-med-stock">
        <form id="add-med-stock-form" method="post" action="">
            <h3 class="admin-function-close">X</h3>
            <h3>Add Medicine Stock</h3>
            <input type="text" name="stock-name" id="stock-name" placeholder="Medicine Name" title="Medicine Name" /> <br />
            <input type="hidden" name="stock-med-id" id="stock-med-id" value="" /> <br />
            <label for="stock-mfg">Date of Manufacture:</label> <br />
            <input type="date" name="stock-mfg" id="stock-mfg" title="Date of Manufacture" /> <br />
            <label for="stock-mfg">Date of Expiry:</label> <br />
            <input type="date" name="stock-exp" id="stock-exp" title="Date of Expiry" /> <br />
            <label for="stock-mfg">Date of Arrival:</label> <br />
            <input type="date" name="stock-arr" id="stock-arr" title="Date of Arrival" /> <br /> 
            <input type="number" name="stock-num" id="stock-num" placeholder="Number of meds" title="Number of meds" /> <br />

            <input type="submit" name="stock-add" value="Add Stock" />
            <input type="reset" value="Reset" /> <br />
        </form>
    </div>

    <div class="function" id="add-med">
        <form id="add-med-form" action="" method="post">
            <h3 class="admin-function-close">X</h3>
            <h3>Add New Medicine</h3>
            <input type="text" name="med-name" id="med-name" placeholder="Medicine name" title="Medicine name" /> <br />
            <input type="text" name="med-manufacturer" id="med-manufacturer" placeholder="Medicine Manufacturer" title="Medicine Manufacturer" /> <br />

            <input type="submit" name="med-add" value="Add" />
            <input type="reset" value="Reset" /> <br />
        </form>
    </div>

    <div class="function" id="reset-user-pass">
        <form id="reset-user-pass-form" method="post" action="">
            <h3 class="admin-function-close">X</h3>
            <h3>Reset User Password</h3>
            <input type="text" name="reset-username" id="reset-username" placeholder="Email or Rollnumber/ Employee id" title="Email or Rollnumber/ Employee id">            
            <input type="submit" name="reset-sub" value="Reset Password" />
            <input type="reset" value="Clear" />
        </form>
    </div>

    <div class="function" id="get-user-history">
        <form id="get-user-history-form" method="post" action="">
            <h3 class="admin-function-close">X</h3>
            <h3>Get User's Medical History</h3>
            <input type="text" name="history-username" id="history-username" placeholder="Email or Rollnumber/ Employee id" title="Email or Rollnumber/ Employee id">
            <input type="hidden" name="history-user-id" id="history-user-id" value="" />
            <input type="submit" name="history-sub" value="Get History" />
            <input type="reset" value="Clear" />
        </form>
    </div>

    <div class="function" id="add-relation">
        <form id="add-relation-form" method="post" action="">
            <h3 class="admin-function-close">X</h3>
            <h3>Add a new Relation</h3>
            <input type="text" name="rel-name" id="rel-name" placeholder="Enter a new relationship" title="Enter a new relationship">
            <input type="submit" name="rel-sub" value="Add Relation" />
            <input type="reset" value="Clear" />
        </form>
    </div>

    <div class="function" id="del-last-record">
        <form id="del-last-record-form" method="post" action="">
            <h3 class="admin-function-close">X</h3>
            <h3>Delete Last Medical Record</h3>
            <p>You can only delete the latest medical record</p>
            <label for="del-idnum">Confirm last record's user:</label>
            <input type="text" name="del-idnum" id="del-idnum" placeholder="Rollnumber/ Employee ID or Email" title="Rollnumber/ Employee ID or Email" />
            <br /><input type="submit" name="del-sub" value="Delete Record" />
            <input type="reset" value="Clear" />
        </form>
    </div>

    <div id="get-contact" class="function">
        <form id="get-contact-form" method="post" action="">
            <h3 class="admin-function-close">X</h3>
            <h3>Get User Emergency Contacts</h3>
            <input type="text" name="cont-username" id="cont-username" placeholder="Rollnumber/ Employee ID or Email" title="Rollnumber/ Employee ID or Email" />
            <input type="hidden" name="cont-user-id" id="cont-user-id" />
            <br /><input type="submit" value="Get Contacts" name="cont-sub" />
            <input type="reset" value="Reset" />
         </form>
    </div>

    <div id="get-user-details" class="function">
        <form id="get-user-details-form" method="post" action="">
            <h3 class="admin-function-close">X</h3>
            <h3>Get User Details</h3>
            <input type="text" name="det-username" id="det-username" placeholder="Rollnumber/ Employee ID or Email" title="Rollnumber/ Employee ID or Email" />
            <input type="hidden" name="det-user-id" id="det-user-id" />
            <br /><input type="submit" value="Get Details" name="det-sub" />
            <input type="reset" value="Reset" />
         </form>
    </div>
</div>

<script src="/JS/admin.js"></script>
        

