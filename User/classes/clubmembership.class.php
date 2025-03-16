<?php
require_once 'database.php';

class ClubMembership
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Function to count the clubs for a specific user ID
    public function countClubsByUserID($userID)
    {
        try {
            $query = "SELECT COUNT(*) AS totalClubs FROM club_membership WHERE userID = :userID AND cmStatus = 'Approved'";
            $stmt = $this->db->connect()->prepare($query);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
    
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['totalClubs'];
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            echo "Error counting clubs: " . $e->getMessage();
            return false;
        }
    }
    
}
?>
