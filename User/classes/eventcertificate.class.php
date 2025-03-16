<?php
require_once 'database.php';

class EventCertificate
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function countCertificatesByUserID($userID)
    {
        try {
            $query = "SELECT COUNT(*) as totalCertificates FROM event_certificate WHERE userID = :userID";
            $stmt = $this->db->connect()->prepare($query);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['totalCertificates'];
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            echo "Error counting certificates: " . $e->getMessage();
            return false;
        }
    }
}
?>
