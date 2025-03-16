<?php

require_once 'database.php';

class EventAttendance{
    //attributes

    public $eventAttendanceID;
    public $userID;
    public $dateEntered;
    public $timeEntered;

    protected $db;
    
    function __construct()
    {
        $this->db = new Database();
    }

    //Methods
    public function fetchUserAttendance($userID) {
        try {
            $sql = "SELECT ea.eventAttendanceID, e.eventName, ea.timeEntered, ea.dateEntered
                    FROM event_attendance ea
                    INNER JOIN events e ON ea.eventID = e.eventID
                    WHERE ea.userID = :userID";

            $stmt = $this->db->connect()->prepare($sql);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            echo "Error fetching user attendance: " . $e->getMessage();
            return false;
        }
    }

    public function fetch($eventID) {
        try {
            $stmt = $this->db->connect()->prepare("SELECT * FROM event WHERE eventID = :eventID");
            $stmt->bindParam(':eventID', $eventID, PDO::PARAM_INT);
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


    function showEventAttendance()
    {
        $sql = "SELECT e.*, CONCAT(u.userFirstName, ' ', u.userMiddleName, ' ', u.userLastName) AS fullName 
                FROM event_attendanceuser e 
                INNER JOIN user u ON e.userID = u.userID 
                ORDER BY e.timeEntered DESC";
        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data ?: []; // Return an empty array if $data is falsy
    }
    

    function getAllEvents() {
        $sql = "SELECT * FROM event";
        $query = $this->db->connect()->prepare($sql);
    
        $events = null;
    
        if ($query->execute()) {
            $events = $query->fetchAll();
        }
    
        return $events;
    }

    public function getDb() {
        return $this->db;
    }
}

?>
