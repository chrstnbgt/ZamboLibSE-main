<?php

require_once 'database.php';

class EventProposal{
    //attributes

    public $proposalID;
    public $organizationClubID;
    public $proposalSubject;
    public $proposalDescription;
    public $proposalFile;
    public $status;



    protected $db;
    function __construct()
    {
        $this->db = new Database();
    }

    //Methods

    public function add() {
        $conn = $this->db->connect(); // Get the database connection from the Database class
    
        $stmt = $conn->prepare("INSERT INTO proposal (proposalID, organizationClubID, proposalSubject, proposalDescription, proposalFile, status) VALUES 
        (:proposalID, :organizationClubID, :proposalSubject, :proposalDescription, :proposalFile, :status)");
        $stmt->bindParam(':proposalID', $this->proposalID);
        $stmt->bindParam(':organizationClubID', $this->organizationClubID);
        $stmt->bindParam(':proposalSubject', $this->proposalSubject);
        $stmt->bindParam(':proposalDescription', $this->proposalDescription);
        $stmt->bindParam(':proposalFile', $this->proposalFile);
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            return true;
        } 
        else {
            return false;
        }
    }

    public function fetch($proposalID) {
        try {
            $stmt = $this->db->connect()->prepare("SELECT * FROM proposal WHERE proposalID = :proposalID");
            $stmt->bindParam(':proposalID', $proposalID, PDO::PARAM_INT);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($result)) {
                return false; // Event not found
            }
    
            return $result[0];
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            echo "Error fetching event: " . $e->getMessage();
            return false;
        }
    }

    function show()
    {
        $sql = "SELECT * FROM proposal ORDER BY proposalCreatedAt DESC;";
        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data ?: []; // Return an empty array if $data is falsy
    }

    public function checkEventOverlap($organizationClubID, $startDate, $startTime, $endDate, $endTime) {
        try {
            // Prepare SQL query to check for overlap
            $sql = "SELECT COUNT(*) FROM events WHERE organizationClubID = :organizationClubID AND (
                        (eventStartDate <= :startDate AND eventEndDate >= :endDate)
                        OR (eventStartDate <= :endDate AND eventEndDate >= :startDate)
                    ) AND (
                        (eventStartTime <= :endTime AND eventEndTime >= :startTime)
                        OR (eventStartTime <= :startTime AND eventEndTime >= :endTime)
                    )";
            
            $stmt = $this->db->connect()->prepare($sql);
            $stmt->bindParam(':organizationClubID', $organizationClubID);
            $stmt->bindParam(':startDate', $startDate);
            $stmt->bindParam(':startTime', $startTime);
            $stmt->bindParam(':endDate', $endDate);
            $stmt->bindParam(':endTime', $endTime);
            $stmt->execute();
            
            // Fetch the count
            $count = $stmt->fetchColumn();
            
            // Return true if there is an overlap, false otherwise
            if ($count > 0) {
                return "The proposed date and time conflict with existing events. Please choose a different date or time.";
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // Handle the exception
            error_log("Error checking event overlap: " . $e->getMessage());
            return "An error occurred while checking event overlap. Please try again later.";
        }
    }
    
    



}

?>
