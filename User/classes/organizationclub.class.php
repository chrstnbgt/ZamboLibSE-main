<?php

require_once 'database.php';

class OrganizationClub {

    public $organizationClubID;
    public $userID;
    public $orgClubImage;
    public $ocName;
    public $ocEmail;
    public $ocContactNumber;

    public $organizationClubType;
    
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    function addOrgClub()
    {
        $sql = "INSERT INTO organization_club (userID, orgClubImage, ocName, ocEmail, ocContactNumber) 
                VALUES (:userID, :orgClubImage, :ocName, :ocEmail, :ocContactNumber);";
    
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':userID', $this->userID);
        $query->bindParam(':orgClubImage', $this->orgClubImage);
        $query->bindParam(':ocName', $this->ocName);
        $query->bindParam(':ocEmail', $this->ocEmail);
        $query->bindParam(':ocContactNumber', $this->ocContactNumber);
        // $query->bindParam(':organizationClubType', $this->organizationClubType);
        
    
        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }


    
    public function fetchOrganizationClubs($userID) {
        try {
            $stmt = $this->db->connect()->prepare("SELECT * FROM organization_club WHERE userID = :userID");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($result)) {
                return false; // No organization clubs found for the user
            }
    
            return $result;
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            echo "Error fetching organization clubs: " . $e->getMessage();
            return false;
        }
    }

    public function fetchApprovedOrganizationClubs($userID) {
        try {
            $stmt = $this->db->connect()->prepare("SELECT * FROM organization_club WHERE userID = :userID AND ocStatus = 'Approved'");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($result)) {
                return false; // No approved organization clubs found for the user
            }
    
            return $result;
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            echo "Error fetching approved organization clubs: " . $e->getMessage();
            return false;
        }
    }
    

    function fetchOrganizationDetails($organizationClubID)
    {
        $sql = "SELECT * FROM organization_club WHERE organizationClubID = :organizationClubID;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':organizationClubID', $organizationClubID);
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }
    function edit(){
        // Correct SQL query with proper table name and no extra comma
        $sql = "UPDATE organization_club SET 
                ocName = :ocName, 
                orgClubImage = :orgClubImage, 
                ocEmail = :ocEmail, 
                ocContactNumber = :ocContactNumber 
                WHERE organizationClubID = :organizationClubID";
    
        // Prepare the SQL query
        $query = $this->db->connect()->prepare($sql);
    
        // Bind parameters correctly
        $query->bindParam(':ocName', $this->ocName);
        $query->bindParam(':orgClubImage', $this->orgClubImage);
        $query->bindParam(':ocEmail', $this->ocEmail);
        $query->bindParam(':ocContactNumber', $this->ocContactNumber);
        $query->bindParam(':organizationClubID', $this->organizationClubID, PDO::PARAM_INT); // Fix: Bind ID
    
        // Execute the query and return true if successful, false otherwise
        return $query->execute();
    }
    
    
}

