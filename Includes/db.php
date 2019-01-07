<?php
    //Create all the tables
    //---------------------------

    //Table: users
    //CT01
    //---------------------------

    $q = "CREATE TABLE IF NOT EXISTS users (";
    $q .= "id INT PRIMARY KEY AUTO_INCREMENT,";
    $q .= "fname VARCHAR(35) NOT NULL,";
    $q .= "lname VARCHAR(35),";
    $q .= "email VARCHAR(55),";
    $q .= "password VARCHAR(100),";
    $q .= "idnum VARCHAR(15),";                     //Roll number or Employ id
    $q .= "residence VARCHAR(20),";
    $q .= "room VARCHAR(10),";
    $q .= "mob1 VARCHAR(10),";
    $q .= "mob2 VARCHAR(10),";
    $q .= "address VARCHAR(250),";
    $q .= "dob DATE,";
    $q .= "gender VARCHAR(7))";

    $conn->query($q) or die("Error CT01. Contact admins.");

    //Table: contacts
    //CT02
    //---------------------------

    $q = "CREATE TABLE IF NOT EXISTS contacts (";
    $q .= "id INT PRIMARY KEY AUTO_INCREMENT,";
    $q .= "fname VARCHAR(35) NOT NULL,";
    $q .= "lname VARCHAR(35),";
    $q .= "mob VARCHAR(10))";

    $conn->query($q) or die("Error CT02. Contact admins.");

    //Table: user_roles
    //CT03
    //---------------------------

    $q = "CREATE TABLE IF NOT EXISTS user_roles (";
    $q .= "id INT PRIMARY KEY AUTO_INCREMENT,";
    $q .= "role VARCHAR(17) NOT NULL)";
    
    $conn->query($q) or die("Error CT03. Contact admins.");

    //Table: relations
    //CT04
    //---------------------------

    $q = "CREATE TABLE IF NOT EXISTS relations (";
    $q .= "id INT PRIMARY KEY AUTO_INCREMENT,";
    $q .= "relation VARCHAR(20) NOT NULL)";
    
    $conn->query($q) or die("Error CT04. Contact admins.");

    //Table: users_in_roles
    //CT05
    //---------------------------

    $q = "CREATE TABLE IF NOT EXISTS users_in_roles (";
    $q .= "id INT PRIMARY KEY AUTO_INCREMENT,";
    $q .= "user_id INT NOT NULL,";
    $q .= "role_id INT NOT NULL,";
    $q .= "FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,";
    $q .= "FOREIGN KEY (role_id) REFERENCES user_roles (id) ON DELETE CASCADE)";

    $conn->query($q) or die("Error CT05. Contact admins.");

    //Table: users_in_relation
    //CT06
    //---------------------------

    $q = "CREATE TABLE IF NOT EXISTS users_in_relation (";
    $q .= "id INT PRIMARY KEY AUTO_INCREMENT,";
    $q .= "user_id INT NOT NULL,";
    $q .= "contact_id INT NOT NULL,";
    $q .= "relation_id INT NOT NULL,";
    $q .= "FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,";
    $q .= "FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE CASCADE,";
    $q .= "FOREIGN KEY (relation_id) REFERENCES relations (id) ON DELETE CASCADE)";

    $conn->query($q) or die("Error CT06. Contact admins.");

    //Table: medical_records
    //CT07
    //---------------------------

    $q = "CREATE TABLE IF NOT EXISTS medical_records (";
    $q .= "id INT PRIMARY KEY AUTO_INCREMENT,";
    $q .= "doc_notes VARCHAR(250),";
    $q .= "mrdate DATETIME,";
    $q .= "doc_id INT NOT NULL,";
    $q .= "user_id INT NOT NULL,";
    $q .= "FOREIGN KEY (doc_id) REFERENCES users (id) ON DELETE CASCADE,";
    $q .= "FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE)";
    
    $conn->query($q) or die("Error CT07. Contact admins.");

    //Table: medicines
    //CT08
    //---------------------------

    $q = "CREATE TABLE IF NOT EXISTS medicines (";
    $q .= "id INT PRIMARY KEY AUTO_INCREMENT,";
    $q .= "name VARCHAR(50),";
    $q .= "manufacturer VARCHAR(30),";
    $q .= "UNIQUE(name))";

    $conn->query($q) or die("Error CT08. Contact admins.");

    //Table: med_batch
    //CT09
    //---------------------------

    $q = "CREATE TABLE IF NOT EXISTS med_batch (";
    $q .= "id INT PRIMARY KEY AUTO_INCREMENT,";
    $q .= "exp_date DATE NOT NULL,";
    $q .= "arr_date DATE NOT NULL,";                //date on which the stock arrived at the HC
    $q .= "entry DATETIME NOT NULL,";               //Time when data was entered
    $q .= "stock_num INT NOT NULL,";
    $q .= "init_stock INT NOT NULL,";
    $q .= "price INT NOT NULL,";                    //Total cost of the batch
    $q .= "med_id INT NOT NULL,";
    $q .= "seller VARCHAR(50),";
    $q .= "FOREIGN KEY (med_id) REFERENCES medicines (id) ON DELETE CASCADE)";

    $conn->query($q) or die("Error CT09. Contact admins.");

    //Table: prescriptions
    //CT10
    //---------------------------

    $q = "CREATE TABLE IF NOT EXISTS prescriptions (";
    $q .= "id INT PRIMARY KEY AUTO_INCREMENT,";
    $q .= "mr_id INT NOT NULL,";                        //medical record
    $q .= "mb_id INT NOT NULL,";                        //medicine batch
    $q .= "number INT,";
    $q .= "af_food TINYINT,";
    $q .= "fn TINYINT,";                                //forenoon
    $q .= "an TINYINT,";                                //afternoon   
    $q .= "nt TINYINT,";                                //night                      
    $q .= "FOREIGN KEY (mr_id) REFERENCES medical_records (id) ON DELETE CASCADE,";
    $q .= "FOREIGN KEY (mb_id) REFERENCES med_batch (id) ON DELETE CASCADE)";

    $conn->query($q) or die("Error CT10. Contact admins.");

    //Table: med_certificate
    //CT11
    //---------------------------

    $q = "CREATE TABLE IF NOT EXISTS med_certificate (";
    $q .= "id INT PRIMARY KEY AUTO_INCREMENT,";
    $q .= "mr_id INT NOT NULL,"; 
    $q .= "issue_date DATETIME NOT NULL,";
    $q .= "start_date DATE NOT NULL,";
    $q .= "duration TINYINT NOT NULL,";
    $q .= "rec_ref VARCHAR(20),";                           //Receipt reference number
    $q .= "FOREIGN KEY (mr_id) REFERENCES medical_records (id) ON DELETE CASCADE)";

    $conn->query($q) or die("Error CT11. Contact admins.");

    //Table: med_transaction
    //CT12
    //----------------------------

    $q = "CREATE TABLE IF NOT EXISTS med_transaction (";
    $q .= "id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,";
    $q .= "batch_id INT NOT NULL,";
    $q .= "num SMALLINT UNSIGNED NOT NULL,";
    $q .= "tr_date DATETIME NOT NULL,";
    $q .= "FOREIGN KEY (batch_id) REFERENCES med_batch (id) ON DELETE CASCADE)";

    $conn->query($q) or die("Error CT12. Contact admins. " . $conn->error);


    //Insert some required data
    //============================

    $q = "INSERT INTO user_roles (id, role) ";
    $q .= "SELECT * FROM (SELECT 1, 'Admin') AS tmp ";
    $q .= "WHERE NOT EXISTS (";
    $q .= "SELECT role FROM user_roles WHERE role = 'Admin'";
    $q .= ") LIMIT 1";
   
    $conn->query($q) or die("Admin creation failed (1.1). Contact admins. Error: " . $conn->error);

    $q = "INSERT INTO user_roles (id, role) ";
    $q .= "SELECT * FROM (SELECT 2, 'Doctor') AS tmp ";
    $q .= "WHERE NOT EXISTS (";
    $q .= "SELECT role FROM user_roles WHERE role = 'Doctor'";
    $q .= ") LIMIT 1";
   
    $conn->query($q) or die("Doctor creation failed (1.2). Contact admins. Error: " . $conn->error);

    $q = "INSERT INTO user_roles (id, role) ";
    $q .= "SELECT * FROM (SELECT 3, 'Nurse/Pharmacist') AS tmp ";
    $q .= "WHERE NOT EXISTS (";
    $q .= "SELECT role FROM user_roles WHERE role = 'Nurse/Pharmacist'";
    $q .= ") LIMIT 1";
   
    $conn->query($q) or die("Nurse creation failed (1.3). Contact admins. Error: " . $conn->error);

    $q = "INSERT INTO user_roles (id, role) ";
    $q .= "SELECT * FROM (SELECT 4, 'Employee') AS tmp ";
    $q .= "WHERE NOT EXISTS (";
    $q .= "SELECT role FROM user_roles WHERE role = 'Employee'";
    $q .= ") LIMIT 1";
   
    $conn->query($q) or die("Employee creation failed (1.4). Contact admins. Error: " . $conn->error);

    $q = "INSERT INTO user_roles (id, role) ";
    $q .= "SELECT * FROM (SELECT 5, 'Student') AS tmp ";
    $q .= "WHERE NOT EXISTS (";
    $q .= "SELECT role FROM user_roles WHERE role = 'Student'";
    $q .= ") LIMIT 1";
   
    $conn->query($q) or die("Student creation failed (1.5). Contact admins. Error: " . $conn->error);

    $default_admin_email = "admin";
    $default_admin_password = "admin";

    $query_check_admin_exists = "SELECT id FROM users WHERE email = ? LIMIT 1";
    $statement_check_admin_exists = $conn->prepare($query_check_admin_exists);
    $statement_check_admin_exists->bind_param('s', $default_admin_email);
    $statement_check_admin_exists->execute();
    $statement_check_admin_exists->store_result();
    if($statement_check_admin_exists->num_rows == 0)
    {
        $query_insert_admin = "INSERT INTO users (fname, email, password) VALUES ('Admin', ?, SHA(?))";
        $statement_insert_admin = $conn->prepare($query_insert_admin);
        $statement_insert_admin->bind_param('ss', $default_admin_email, $default_admin_password);
        $statement_insert_admin->execute();
        $statement_insert_admin->store_result();

        $admin_user_id = $statement_insert_admin->insert_id;
        $query_add_admin_to_role = "INSERT INTO users_in_roles(user_id, role_id) VALUES (?, 1)";
        $statement_add_admin_to_role = $conn->prepare($query_add_admin_to_role);
        $statement_add_admin_to_role->bind_param('d', $admin_user_id);
        $statement_add_admin_to_role->execute();
        $statement_add_admin_to_role->close();
    }
?>
