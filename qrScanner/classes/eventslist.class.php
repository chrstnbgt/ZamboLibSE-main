<?php
require_once 'database.php';

// Class for Event List
class EventList {
    private $conn; // Define the database connection property

    public function __construct($db){
        $this->conn = $db; // Assign the PDO connection directly
    }

    // Get list of events for a given librarian
    public function getEventsForLibrarian2($librarianID){
        $query = "SELECT e.eventID, e.eventTitle
        FROM event e
        JOIN event_attendance ea ON e.eventID = ea.eventID
        JOIN `event_attendance-checker` eac ON ea.eventAttendanceID = eac.eventAttendanceID
        WHERE eac.librarianID = :librarianID";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":librarianID", $librarianID);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEventsForLibrarian($librarianID){
        $query = "SELECT e.eventID, e.eventTitle
                  FROM event e
                  JOIN event_facilitator ef ON e.eventID = ef.eventID
                  WHERE ef.librarianID = :librarianID";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":librarianID", $librarianID);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function attendanceExists($eventID) {
        $query = "SELECT COUNT(*) FROM event_attendance WHERE eventID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$eventID]);
        $count = $stmt->fetchColumn();
        return $count > 0;
    }


    public function addAttendanceDays($eventID) {
        // Get event details from the event table
        $query = "SELECT eventStartDate, eventEndDate, eventStartTime, eventEndTime FROM event WHERE eventID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$eventID]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($event) {
            $startDate = new DateTime($event['eventStartDate']);
            $endDate = new DateTime($event['eventEndDate']);
            $startTime = new DateTime($event['eventStartTime']);
            $endTime = new DateTime($event['eventEndTime']);

            // Iterate over the days between start and end date
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                // Insert record into event_attendance table
                $query = "INSERT INTO event_attendance (eventID, eaDate, eaTime) VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$eventID, $currentDate->format('Y-m-d'), $startTime->format('H:i')]);

                // Increment current date by one day
                $currentDate->modify('+1 day');
            }
            echo "Records added successfully.";
        } else {
            echo "Event not found.";
        }
    }

    public function getAttendanceData($eventID) {
        $query = "SELECT * FROM event_attendance WHERE eventID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$eventID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}

// Model for Event Attendance Checker
class EventAttendanceChecker {
    private $conn; // Define the database connection property

    public function __construct($db){
        $this->conn = $db; // Assign the PDO connection directly
    }
}

// Model for Event
class Event {
    private $conn; // Define the database connection property

    public function __construct($db){
        $this->conn = $db; // Assign the PDO connection directly
    }

    // Get list of events for a given librarian
    public function getEventsForLibrarian($librarianID){
        try {
            $query = "SELECT e.eventID, e.eventTitle
                      FROM event_facilitator ef
                      JOIN event e ON e.eventID = ef.eventID
                      WHERE ef.librarianID = :librarianID";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":librarianID", $librarianID);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getEventDetails($eventID) {
        $query = "SELECT * FROM event WHERE eventID = :eventID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":eventID", $eventID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
