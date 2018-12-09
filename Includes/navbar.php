<div id="nav-bar">
    <div id="nav-left"></div>
    <div id="nav-right">
        <ul>
            <li>Hello, <?php echo $_SESSION["fname"];?></li>|
            <?php echo (isAdmin() || isNurse())? '<li><b><a href="/pharmacy.php">PHARMACY</a></b></li>' : ''; ?>            
            <li><a href="/home.php" title="Go to the home page">Home</a></li>
            <li><a href="/profile.php" title="Edit your personal info">Profile</a></li>
            <li><a href="/logout.php">Logout</a></li>
        </ul>
    </div>
</div>