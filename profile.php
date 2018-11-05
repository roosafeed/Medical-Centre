<?php
    $GLOBALS["page"] = "Profile";
    include_once("Includes/header.php");
    if(!isloggedin())
    {
        header("Location: index.html");
    }

    $qbio = "SELECT * FROM users WHERE id = ?";
    $qbio = $conn->prepare($qbio);
    $qbio->bind_param("d", $_SESSION["userid"]);
    $qbio->execute();
    $bio_res = $qbio->get_result();
    $qbio->close();
    $bio_res = $bio_res->fetch_assoc();

?>

<div id="profile-buttons">
    <div class="button" id="toggle-up-bio">
        <h2>Edit Bio</h2>
    </div>
    <div class="button" id="toggle-ch-pass">
        <h2>Change Password</h2>
    </div>
    <div class="button" id="toggle-add-cont">
        <h2>Add Contacts</h2>
    </div>
</div>

<div id="profile-functions">
    <div class="function" id="up-bio">
        <form id="up-bio-form" method="post" action="">
            <h3 class="profile-function-close">X</h3>
            <h3>Update Your Bio</h3>
            <label for="fname">First Name:</label><br />
            <input type="text" name="fname" required id="fname" placeholder="First Name" title="First Name" value="<?php echo $bio_res["fname"] ;?>" /> <br />
            <label for="lname">Last Name:</label><br />
            <input type="text" name="lname" required id="lname" placeholder="Last Name" title="Last Name" value="<?php echo $bio_res["lname"] ;?>" /> <br />
            <label for="residence">Hostel/ Quarters name:</label><br />
            <input type="text" name="residence" id="residence" placeholder="Hostel/ Residence" title="Hostel/ Residence" value="<?php echo $bio_res["residence"] ;?>" /> <br />
            <label for="room">Room/Quarters number:</label><br />
            <input type="text" name="room" id="room" placeholder="Hostel room number" title="Hostel room number" value="<?php echo $bio_res["room"] ;?>" /> <br />
            <label for="mob1">Mobile Number 1:</label><br />
            <input type="text" name="mob1" required id="mob1" placeholder="Mobile Number" title="Mobile Number" value="<?php echo $bio_res["mob1"] ;?>" /> <br />
            <label for="mob2">Mobile Number 2:</label><br />
            <input type="text" name="mob2" id="mob2" placeholder="Mobile Number 2" title="Mobile Number 2" value="<?php echo $bio_res["mob2"] ;?>" /> <br />
            <label for="address">Home Address:</label><br />
            <textarea name="address" id="address" placeholder="Home address" title="Home address"><?php echo $bio_res["address"] ;?></textarea> <br />

            <input type="submit" name="up-bio-sub" value="Update" /> 
        </form>
    </div>

    <div class="function" id="ch-pass">
        <form id="ch-pass-form" method="post" action="">
            <h3 class="profile-function-close">X</h3>
            <h3>Change Password</h3>
            <label for="old-pass">Old Password:</label><br />
            <input type="password" name="old-pass" id="old-pass" required autocomplete="off" /><br />
            <label for="new-pass">New Password:</label><br />
            <input type="password" name="new-pass" id="new-pass" required autocomplete="off" /><br />
            <label for="new-pass-con">Confirm New Password:</label><br />
            <input type="password" name="new-pass-con" id="new-pass-con" required autocomplete="off" /><br />

            <input type="submit" name="ch-pass-sub" value="Change Password" />
        </form>
    </div>

    <div class="function" id="add-cont">
        <form id="ch-pass-form" method="post" action="">
            <h3 class="profile-function-close">X</h3>
            <h3>Add Contacts</h3>
            <select name="new-rel" id="new-rel">            
            <?php
                $qrel = "SELECT * FROM relations";
                $qrel = $conn->query($qrel);
                while($rows = $qrel->fetch_assoc())
                {
                    echo '<option value="' . $rows["id"] . '">' . $rows["relation"] . '</option>';
                }
                $qrel->close();
            ?>
            </select><br />
            <input type="text" name="new-cont-fname" id="new-cont-fname" placeholder="Contact's first name" title="Contact's first name" required /><br />
            <input type="text" name="new-cont-lname" id="new-cont-lname" placeholder="Contact's last name" title="Contact's last name" /><br />
            <input type="text" name="new-cont-mob" id="new-cont-name" placeholder="Contact's mobile number" title="Contact's mobile number" required /><br />

            <input type="submit" value="Add Contact" name="add-cont-sub" />
            <input type="reset" value="Clear" />
        </form>
    </div>
</div>

<script src="/JS/profile.js"></script>
<?php
    include_once("Includes/footer.php");
?>
