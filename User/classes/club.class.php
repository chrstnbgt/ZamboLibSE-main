<?php

require_once 'database.php';

class Clubs {
    private $db;

    public $clubID;
    public $clubName;
    public $clubDescription;
    public $clubMinAge;
    public $clubMaxAge;
    // public $librarianIDs;

    public function __construct() {
        $this->db = new Database();
    }

    public function edit($clubID, $clubName, $clubDescription, $librarianIDs, $clubMinAge, $clubMaxAge) {
        $conn = $this->db->connect();
    
        // Start a transaction
        $conn->beginTransaction();
    
        try {
            // Update club details
            $sql = "UPDATE club SET clubName = :clubName, clubDescription = :clubDescription, clubMinAge = :clubMinAge, clubMaxAge = :clubMaxAge WHERE clubID = :clubID";
            $query = $conn->prepare($sql);
            $query->bindParam(':clubID', $clubID);
            $query->bindParam(':clubName', $clubName);
            $query->bindParam(':clubDescription', $clubDescription);
            $query->bindParam(':clubMinAge', $clubMinAge);
            $query->bindParam(':clubMaxAge', $clubMaxAge);
            $query->execute();
    
            // Delete existing club management records
            $deleteStmt = $conn->prepare("DELETE FROM club_management WHERE clubID = :clubID");
            $deleteStmt->bindParam(':clubID', $clubID);
            $deleteStmt->execute();
    
            // Insert new club management records
            foreach ($librarianIDs as $librarianID) {
                $insertStmt = $conn->prepare("INSERT INTO club_management (clubID, librarianID) VALUES (:clubID, :librarianID)");
                $insertStmt->bindParam(':clubID', $clubID);
                $insertStmt->bindParam(':librarianID', $librarianID);
                $insertStmt->execute();
            }
    
            // Commit the transaction
            $conn->commit();
            return true;
        } catch (PDOException $e) {
            // Roll back the transaction on error
            $conn->rollback();
            return false;
        }
    }

    function showMembership($userID)
    {
        try {
            $sql = "SELECT c.*, cm.userID AS member, cm.cmStatus
                    FROM club c 
                    LEFT JOIN club_membership cm ON c.clubID = cm.clubID AND cm.userID = :userID
                    WHERE cm.cmStatus IN ('Pending', 'Approved', 'Rejected')";
            $query = $this->db->connect()->prepare($sql);
            $query->bindParam(':userID', $userID, PDO::PARAM_INT);
            $data = [];
    
            if ($query->execute()) {
                $data = $query->fetchAll();
            }
            return $data;
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            error_log("Error fetching clubs: " . $e->getMessage());
            return [];
        }
    }
    


    function deleteMembership($userID, $clubID) {
        try {
            // Start a transaction
            $this->db->connect()->beginTransaction();
    
            // Delete from club_membership table
            $sql1 = "DELETE FROM club_membership WHERE userID = :userID AND clubID = :clubID";
            $query1 = $this->db->connect()->prepare($sql1);
            $query1->bindParam(':userID', $userID);
            $query1->bindParam(':clubID', $clubID);
            $query1->execute();
    
            // Get clubMembershipID
            $sql2 = "SELECT clubMembershipID FROM club_membership WHERE userID = :userID AND clubID = :clubID";
            $query2 = $this->db->connect()->prepare($sql2);
            $query2->bindParam(':userID', $userID);
            $query2->bindParam(':clubID', $clubID);
            $query2->execute();
            $clubMembershipID = $query2->fetch(PDO::FETCH_COLUMN);
    
            // Delete from club_formanswer table
            $sql3 = "DELETE FROM club_formanswer WHERE clubMembershipID = :clubMembershipID";
            $query3 = $this->db->connect()->prepare($sql3);
            $query3->bindParam(':clubMembershipID', $clubMembershipID);
            $query3->execute();
    
            // Commit the transaction
            $this->db->connect()->commit();
    
            return true;
        } catch (PDOException $e) {
            // Rollback the transaction in case of error
            $this->db->connect()->rollBack();
            error_log("Error deleting membership: " . $e->getMessage());
            return false;
        }
    }
    
    


    public function fetch($clubID) {
        $stmt = $this->db->connect()->prepare("
            SELECT c.*, COUNT(cm.userID) as total_members
            FROM club c
            LEFT JOIN club_membership cm ON c.clubID = cm.clubID
            WHERE c.clubID = :clubID
            GROUP BY c.clubID
        ");
        $stmt->bindParam(':clubID', $clubID, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    function delete($clubID)
    {
        $sql = "DELETE FROM club WHERE clubID = :clubID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':clubID', $clubID);

        return $query->execute();
    }
    
    function getClubManagers($clubID)
    {
        $sql = "SELECT librarian.* FROM club_management 
                JOIN librarian ON club_management.librarianID = librarian.librarianID 
                WHERE club_management.clubID = :clubID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':clubID', $clubID);
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
    
    function fetchLibrarian($librarianID)
    {
        $sql = "SELECT * FROM librarian WHERE librarianID = :librarianID;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':librarianID', $librarianID);
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }


    function show()
    {
        $sql = "SELECT * FROM club ORDER BY clubName ASC;";
        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data ?: []; // Return an empty array if $data is falsy
    }
    


    function getAllClubs() {
        $sql = "SELECT * FROM club";
        $query = $this->db->connect()->prepare($sql);
    
        $clubs = null;
    
        if ($query->execute()) {
            $clubs = $query->fetchAll();
        }
    
        return $clubs;
    }

    
}
