<?php
    include_once("Includes/initiate.php");

    if(!(isAdmin() || isHCstaff()))
    {
        echo "Unauthorized";
    }
    else
    {
        if(isset($_POST["users"]))
        {
            //handle the post
            $users = json_decode($_POST["users"]);
            $total = count($users);
            $result = array(
                'exist' => array(),
                'success' => array(),
                'fail' => array()
                );

            $qcheck = "SELECT email FROM users WHERE LOWER(email) = LOWER(?) OR LOWER(idnum) = LOWER(?)";
            $qcheck = $conn->prepare($qcheck);

            //Check if users exist in the database
            for($i = 0; $i < $total; $i++)
            {                
                $qcheck->bind_param("ss", $users[$i]->EMAIL, $users[$i]->ID);
                $qcheck->execute();
                $qcheck->store_result();
                $nr = $qcheck->num_rows;
                if($nr > 0)
                {
                    //User exists, add index number to $existing
                    $result["exist"][] = $i;
                }                
            }
            $qcheck->close();

            //get all the user roles
            $qroles = "SELECT id, LOWER(role) AS role FROM user_roles";
            $qroles = $conn->prepare($qroles);
            $qroles->execute();
            $qroles = $qroles->get_result();
            $roles = $qroles->fetch_all();
            $qroles->close();
            
            $rolenames = array();
            for($k = 0; $k < count($roles); $k++)
            {
                $rolenames[] = $roles[$k][1];
            }

            //Add new users to the database
            $qnew = "INSERT INTO users (email, fname, lname, password, idnum, residence, room, mob1, mob2, address, dob, gender) ";
            $qnew .= "VALUES (LOWER(?), ?, ?, SHA(UPPER(?)), UPPER(?), ?, ?, ?, ?, ?, ?, LOWER(?))";
            $qnew = $conn->prepare($qnew);

            //Iterate through the users list............
            $j = 0;
            for($i = 0; $i < $total; $i++)
            {
                //check if the current index number is in the exist array
                if(count($result["exist"]) > $j)
                {
                    if($i == $result["exist"][$j])
                    {
                        //User already exists, skip this user and move on
                        $j++;
                        continue;
                    }                
                }                          
                //Search if the TYPE is valid
                if(in_array(strtolower($users[$i]->TYPE), $rolenames))
                {
                    //Add the user
                    $email = $users[$i]->EMAIL;
                    $fname = $users[$i]->FNAME;
                    $lname = (isset($users[$i]->LNAME))? $users[$i]->LNAME : NULL;
                    $idnum = $users[$i]->ID;
                    $res = (isset($users[$i]->RES))? $users[$i]->RES : NULL;
                    $room = (isset($users[$i]->ROOM))? $users[$i]->ROOM : NULL;
                    $mob1 = (isset($users[$i]->MOB1))? $users[$i]->MOB1 : NULL;
                    $mob2 = (isset($users[$i]->MOB2))? $users[$i]->MOB2 : NULL;
                    $addr = (isset($users[$i]->ADDR))? $users[$i]->ADDR : NULL;
                    $dob = (isset($users[$i]->DOB))? date_format(date_create($users[$i]->DOB), "Y-m-d") : NULL;
                    $gender = (isset($users[$i]->GENDER))? $users[$i]->GENDER : NULL;

                    if(!is_null($gender))
                    {
                        switch($gender)
                        {
                            case 'm':
                                $gender = "male";
                                break;
                            case 'f':
                                $gender = "female";
                                break;
                            case 'o':
                                $gender = "other";
                                break;
                            default:
                                $gender = NULL;
                        }
                    }
                    //email, fname, lname, password, idnum, residence, room, mob1, mob2, address, dob, gender
                    $qnew->bind_param("ssssssssssss", $email, $fname, $lname, $idnum, $idnum, $res, $room, $mob1, $mob2, $addr, $dob, $gender);
                    $qnew->execute();
                    $qnew->store_result();
                    $ar = $qnew->affected_rows;
                    $id = $qnew->insert_id;
                    if($ar == 1)
                    {
                        $role = 0;
                        for($k = 0; $k < count($roles); $k++)
                        {
                            if(strcmp(strtolower($users[$i]->TYPE), strtolower($roles[$k][1])) == 0)
                            {
                                $role = $roles[$k][0];
                                break;
                            }
                        }
                        $qroles = "INSERT INTO users_in_roles (user_id, role_id) VALUES (?,?)";
                        $qroles = $conn->prepare($qroles);
                        $qroles->bind_param("dd", $id, $role);
                        $qroles->execute();
                        $qroles->store_result();
                        $ar2 = $qroles->affected_rows;
                        $qroles->close();

                        if($ar2 == 1)
                        {
                            //Everything is complete
                            $result["success"][] = $i;
                            continue;
                        }
                        else
                        {
                            $qfail = "DELETE FROM users WHERE id = ?";
                            $qfail = $conn->prepare($qfail);
                            $qfail->bind_param("d", $id);
                            $qfail->execute();
                            $qfail->close();
                            $result["fail"][] = $i;
                            continue;
                        }
                    }
                    else
                    {
                        //User add failed
                        $result["fail"][] = $i;
                        continue;
                    }

                }
                else
                {
                    //If the TYPE is invalid, add index to fail array
                    $result["fail"][] = $i;
                }       
            }
            $qnew->close();

            //Send the Rollnumber/Employee ID of the curresponding index numbers in result
            for($i = 0; $i < count($result["exist"]); $i++)
            {
                $result["exist"][$i] = $users[$result["exist"][$i]]->ID;
            }
            for($i = 0; $i < count($result["success"]); $i++)
            {
                $result["success"][$i] = $users[$result["success"][$i]]->ID;
            }
            for($i = 0; $i < count($result["fail"]); $i++)
            {
                $result["fail"][$i] = $users[$result["fail"][$i]]->ID;
            }

            echo json_encode($result);
        }
    }
?>
