<?php

require_once 'database.php';

class User {
    public $userID;
    public $userFirstName;
    public $userMiddleName;
    public $userLastName;
    public $userUserName;
    public $userBirthdate;
    public $userAge;
    public $userEmail;
    public $userGender;
    public $userCivilStatus;
    public $userContactNo;
    // public $userPassword;
    public $userSchoolOffice;
    public $userRegion;
    public $userProvince;
    public $userCity;
    public $userBarangay;
    public $userStreetName;
    public $userZipCode;
    public $userImage;
    public $userType;
    public $userIDCard;


    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    // function signup()
    // {
    //     $sql = "INSERT INTO user (userFirstName, userMiddleName, userLastName, userBirthdate, userAge, userGender, userEmail, userPassword, userContactNo, userOccupation, userSector, userAccountType, userSchoolOffice, userIDCard, userRegion, userProvince, userCity, userBarangay, userStreetName, userZipCode, guardianFirstName, guardianMiddleName, guardianLastName, guardianRole, guardianContactNo, guardianEmail) VALUES 
    //     (:userFirstName, :userMiddleName, :userLastName, :userBirthdate, :userAge, :userGender, :userEmail, :userPassword, :userContactNo, :userOccupation, :userSector, :userAccountType, :userSchoolOffice, :userIDCard, :userRegion, :userProvince, :userCity, :userBarangay, :userStreetName, :userZipCode, :guardianFirstName, :guardianMiddleName, :guardianLastName, :guardianRole, :guardianContactNo, :guardianEmail);";

    //     $query = $this->db->connect()->prepare($sql);
    //     $query->bindParam(':userFirstName', $this->userFirstName);
    //     $query->bindParam(':userMiddleName', $this->userMiddleName);
    //     $query->bindParam(':userLastName', $this->userLastName);
    //     $query->bindParam(':userBirthdate', $this->userBirthdate);
    //     $query->bindParam(':userAge', $this->userAge);
    //     $query->bindParam(':userGender', $this->userGender);
    //     $query->bindParam(':userEmail', $this->userEmail);
    //     // Hash the password securely using password_hash
    //     $hashedPassword = password_hash($this->userPassword, PASSWORD_DEFAULT);
    //     $query->bindParam(':userPassword', $hashedPassword);
    //     $query->bindParam(':userContactNo', $this->userContactNo);
    //     $query->bindParam(':userOccupation', $this->userOccupation);
    //     $query->bindParam(':userSector', $this->userSector);
    //     $query->bindParam(':userAccountType', $this->userAccountType);
    //     $query->bindParam(':userSchoolOffice', $this->userSchoolOffice);
    //     $query->bindParam(':userIDCard', $this->userIDCard);
    //     $query->bindParam(':userRegion', $this->userRegion);
    //     $query->bindParam(':userProvince', $this->userProvince);
    //     $query->bindParam(':userCity', $this->userCity);
    //     $query->bindParam(':userBarangay', $this->userBarangay);
    //     $query->bindParam(':userStreetName', $this->userStreetName);
    //     $query->bindParam(':userZipCode', $this->userZipCode);
    //     $query->bindParam(':guardianFirstName', $this->guardianFirstName);
    //     $query->bindParam(':guardianMiddleName', $this->guardianMiddleName);
    //     $query->bindParam(':guardianLastName', $this->guardianLastName);
    //     $query->bindParam(':guardianRole', $this->guardianRole);
    //     $query->bindParam(':guardianContactNo', $this->guardianContactNo);
    //     $query->bindParam(':guardianEmail', $this->guardianEmail);

    //     if ($query->execute()) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    function signup()
    {
        $sql = "INSERT INTO user (userFirstName, userMiddleName, userLastName, userUserName, userPassword) VALUES 
        (:userFirstName, :userMiddleName, :userLastName, :userUserName, :userPassword);";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':userFirstName', $this->userFirstName);
        $query->bindParam(':userMiddleName', $this->userMiddleName);
        $query->bindParam(':userLastName', $this->userLastName);
        $query->bindParam(':userUserName', $this->userUserName);
        // Hash the password securely using password_hash
        $hashedPassword = password_hash($this->userPassword, PASSWORD_DEFAULT);
        $query->bindParam(':userPassword', $hashedPassword);

        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }


    function signIn()
    {
        $sql = "SELECT * FROM user WHERE userEmail = :userEmail LIMIT 1;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':userEmail', $this->userEmail);
    
        if ($query->execute()) {
            $accountData = $query->fetch(PDO::FETCH_ASSOC);
    
            if ($accountData && password_verify($this->userPassword, $accountData['userPassword'])) {
                $this->userID = $accountData['userID'];
                return true;
            }
        }
    
        return false;
    }

    
    // function edit(){
    //     $sql = "UPDATE user SET 
    //             userLastName=:userLastName, 
    //             userMiddleName=:userMiddleName, 
    //             userFirstName=:userFirstName, 
    //             userEmail=:userEmail, 
    //             userUserName=:userUserName, 
    //             userBirthdate=:userBirthdate, 
    //             userGender=:userGender, 
    //             userCivilStatus=:userCivilStatus, 
    //             userContactNo=:userContactNo, 
    //             -- userPassword=:userPassword, 
    //             userSchoolOffice=:userSchoolOffice, 
    //             -- userIDCard=:userIDCard, 
    //             userRegion=:userRegion, 
    //             userProvince=:userProvince, 
    //             userCity=:userCity, 
    //             userBarangay=:userBarangay, 
    //             userStreetName=:userStreetName, 
    //             userZipCode=:userZipCode, 
    //             userType=:userType, 
    //             -- userImage=:userImage
    //             WHERE userID = :userID;";
    
    //     $query=$this->db->connect()->prepare($sql);
    //     $query->bindParam(':userLastName', $this->userLastName);
    //     $query->bindParam(':userMiddleName', $this->userMiddleName);
    //     $query->bindParam(':userFirstName', $this->userFirstName);
    //     $query->bindParam(':userEmail', $this->userEmail);
    //     $query->bindParam(':userUserName', $this->userUserName);
    //     $query->bindParam(':userBirthdate', $this->userBirthdate);
    //     $query->bindParam(':userGender', $this->userGender);
    //     $query->bindParam(':userCivilStatus', $this->userCivilStatus);
    //     $query->bindParam(':userContactNo', $this->userContactNo);
    //     // $query->bindParam(':userPassword', $this->userPassword);
    //     $query->bindParam(':userSchoolOffice', $this->userSchoolOffice);
    //     // $query->bindParam(':userIDCard', $this->userIDCard);
    //     $query->bindParam(':userRegion', $this->userRegion);
    //     $query->bindParam(':userProvince', $this->userProvince);
    //     $query->bindParam(':userCity', $this->userCity);
    //     $query->bindParam(':userBarangay', $this->userBarangay);
    //     $query->bindParam(':userStreetName', $this->userStreetName);
    //     $query->bindParam(':userZipCode', $this->userZipCode);
    //     $query->bindParam(':userType', $this->userType);
    //     // $query->bindParam(':userImage', $this->userImage);
    //     $query->bindParam(':userID', $this->userID);
    
    //     if($query->execute()){
    //         return true;
    //     }
    //     else{
    //         return false;
    //     }   
    // }

    function edit(){
        // SQL query to update user information in the database
        $sql = "UPDATE user SET 
                userLastName=:userLastName, 
                userMiddleName=:userMiddleName, 
                userFirstName=:userFirstName, 
                userEmail=:userEmail, 
                userUserName=:userUserName, 
                userBirthdate=:userBirthdate, 
                userGender=:userGender, 
                userCivilStatus=:userCivilStatus, 
                userContactNo=:userContactNo, 
                userSchoolOffice=:userSchoolOffice, 
                userRegion=:userRegion, 
                userProvince=:userProvince, 
                userCity=:userCity, 
                userBarangay=:userBarangay, 
                userStreetName=:userStreetName, 
                userZipCode=:userZipCode, 
                -- userType=:userType,
                userImage=:userImage,
                userIDCard=:userIDCard
                WHERE userID = :userID";
    
        // Prepare the SQL query
        $query = $this->db->connect()->prepare($sql);
    
        // Bind parameters for all fields being updated
        $query->bindParam(':userLastName', $this->userLastName);
        $query->bindParam(':userMiddleName', $this->userMiddleName);
        $query->bindParam(':userFirstName', $this->userFirstName);
        $query->bindParam(':userEmail', $this->userEmail);
        $query->bindParam(':userUserName', $this->userUserName);
        $query->bindParam(':userBirthdate', $this->userBirthdate);
        $query->bindParam(':userGender', $this->userGender);
        $query->bindParam(':userCivilStatus', $this->userCivilStatus);
        $query->bindParam(':userContactNo', $this->userContactNo);
        $query->bindParam(':userSchoolOffice', $this->userSchoolOffice);
        $query->bindParam(':userRegion', $this->userRegion);
        $query->bindParam(':userProvince', $this->userProvince);
        $query->bindParam(':userCity', $this->userCity);
        $query->bindParam(':userBarangay', $this->userBarangay);
        $query->bindParam(':userStreetName', $this->userStreetName);
        $query->bindParam(':userZipCode', $this->userZipCode);
        // $query->bindParam(':userType', $this->userType);
        $query->bindParam(':userID', $this->userID);
        $query->bindParam(':userImage', $this->userImage);
        $query->bindParam(':userIDCard', $this->userIDCard);
    
        // Execute the query and return true if successful, false otherwise
        if($query->execute()){
            return true;
        }
        else{
            return false;
        }   
    }
    
    
    
    function delete($userID)
    {
        $sql = "DELETE FROM user WHERE userID = :userID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':userID', $userID);

        return $query->execute();
    }

    function fetch($userID)
    {
        $sql = "SELECT * FROM user WHERE userID = :userID;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':userID', $userID);
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }

    function show()
    {
        $sql = "SELECT * FROM user ORDER BY userLastName ASC, userFirstName ASC;";
        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }

    function isEmailExist()
    {
        $sql = "SELECT * FROM user WHERE userEmail = :userEmail;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':userEmail', $this->userEmail);
        if ($query->execute()) {
            if ($query->rowCount() > 0) {
                return true;
            }
        }
        return false;
    }

    function isUserNameExist()
    {
        $sql = "SELECT * FROM user WHERE userUserName = :userUserName;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':userUserName', $this->userUserName);
        if ($query->execute()) {
            if ($query->rowCount() > 0) {
                return true;
            }
        }
        return false;
    }

    function checkPassword() {
        $sql = "SELECT userPassword FROM user WHERE userID = :userID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':userID', $this->userID);
        $query->execute();

        // Check if the user exists
        if ($query->rowCount() > 0) {
            $hashedPassword = $query->fetchColumn();
            // Use password_verify to check if the entered password matches the hashed password
            return password_verify($this->userPassword, $hashedPassword);
        } else {
            return false; // user not found
        }
    }

    function is_email_exist(){
        $sql = "SELECT * FROM user WHERE userEmail = :userEmail;";
        $query=$this->db->connect()->prepare($sql);
        $query->bindParam(':userEmail', $this->userEmail);
        if($query->execute()){
            if($query->rowCount()>0){
                return true;
            }
        }
        return false;
    }

}

?>
