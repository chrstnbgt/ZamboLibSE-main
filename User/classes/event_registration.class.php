
<?php

require_once 'database.php';

class EventRegistration
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Function to count event registrations by user ID
    public function countEventRegistrationsByUserID($userID)
    {
        try {
            $query = "SELECT COUNT(*) AS totalRegistrations FROM event_registration WHERE userID = :userID";
            $stmt = $this->db->connect()->prepare($query);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['totalRegistrations'];
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            echo "Error counting event registrations: " . $e->getMessage();
            return false;
        }
    }

    public function getEventRegistrationsByUserID($userID)
    {
        try {
            $query = "SELECT er.*, e.eventTitle 
                      FROM event_registration er 
                      JOIN event e ON er.eventID = e.eventID 
                      WHERE er.userID = :userID";
            $stmt = $this->db->connect()->prepare($query);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            echo "Error fetching event registrations: " . $e->getMessage();
            return false;
        }
    }
}
