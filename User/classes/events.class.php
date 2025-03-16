<?php

require_once 'database.php';

class Events{
    //attributes

    public $eventID;
    public $eventTitle;
    public $eventDescription;
    public $librarianIDs;
    public $eventDateTimeIDs;
    public $eventGuestLimit;
    public $eventRegion;
    public $eventProvince;
    public $eventCity;
    public $eventBarangay;
    public $eventStreetName;
    public $eventBuildingName;
    public $eventZipCode;
    public $eventStatus;

    protected $db;
    
    function __construct()
    {
        $this->db = new Database();
    }

    //Methods

    public function add() {
        $conn = $this->db->connect(); // Get the database connection from the Database class
    
        $stmt = $conn->prepare("INSERT INTO event (eventTitle, eventDescription, eventGuestLimit, eventRegion, eventProvince, eventCity, eventBarangay, eventStreetName, eventBuildingName, eventZipCode, eventStatus) VALUES (:eventTitle, :eventDescription, :eventGuestLimit, :eventRegion, :eventProvince, :eventCity, :eventBarangay, :eventStreetName, :eventBuildingName, :eventZipCode, :eventStatus)");
        $stmt->bindParam(':eventTitle', $this->eventTitle);
        $stmt->bindParam(':eventDescription', $this->eventDescription);
        $stmt->bindParam(':eventGuestLimit', $this->eventGuestLimit);
        $stmt->bindParam(':eventRegion', $this->eventRegion);
        $stmt->bindParam(':eventProvince', $this->eventProvince);
        $stmt->bindParam(':eventCity', $this->eventCity);
        $stmt->bindParam(':eventBarangay', $this->eventBarangay);
        $stmt->bindParam(':eventStreetName', $this->eventStreetName);
        $stmt->bindParam(':eventBuildingName', $this->eventBuildingName);
        $stmt->bindParam(':eventZipCode', $this->eventZipCode);
        $stmt->bindParam(':eventStatus', $this->eventStatus);
    
        if ($stmt->execute()) {
            $this->eventID = $conn->lastInsertId();
    
            foreach ($this->librarianIDs as $librarianID) {
                $stmt = $conn->prepare("INSERT INTO event_facilitator (eventID, librarianID) VALUES (:eventID, :librarianID)");
                $stmt->bindParam(':eventID',  $this->eventID);
                $stmt->bindParam(':librarianID', $librarianID);
                $stmt->execute();
            }
            
    
            // Saving event date-times will go here
    
            return true;
        } else {
            return false;
        }
    }

    public function addVolunteer($userID, $eventID) {
        try {
            $conn = $this->db->connect(); // Get the database connection from the Database class
    
            // Prepare SQL statement to insert into event_volunteer table
            $stmt = $conn->prepare("INSERT INTO event_volunteer (userID, eventID) VALUES (:userID, :eventID)");
            $stmt->bindParam(':userID', $userID);
            $stmt->bindParam(':eventID', $eventID);
    
            // Execute the statement
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // Handle the exception
            error_log("Error adding volunteer: " . $e->getMessage());
            return false;
        }
    }

    public function isUserVolunteered($userID, $eventID)
    {
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare("SELECT COUNT(*) FROM event_volunteer WHERE userID = :userID AND eventID = :eventID");
            $stmt->bindParam(':userID', $userID);
            $stmt->bindParam(':eventID', $eventID);
            $stmt->execute();

            $count = $stmt->fetchColumn();

            return $count > 0;
        } catch (PDOException $e) {
            error_log("Error checking if user is volunteered: " . $e->getMessage());
            return false;
        }
    }

    public function cancelVolunteer($userID, $eventID)
{
    try {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("DELETE FROM event_volunteer WHERE userID = :userID AND eventID = :eventID");
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':eventID', $eventID);
        $stmt->execute();

        // Check if any row is affected
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error canceling volunteer application: " . $e->getMessage());
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

    public function checkEventOverlap($organizationClubID, $startDate, $startTime, $endDate, $endTime) {
        try {
            // Prepare SQL query to check for overlap
            $sql = "SELECT COUNT(*) FROM events WHERE organizationClubID = :organizationClubID AND ((eventStartDate <= :endDate AND eventEndDate >= :startDate)
                    OR (eventStartDate <= :startDate AND eventEndDate >= :endDate))
                    AND ((eventStartTime <= :endTime AND eventEndTime >= :startTime)
                    OR (eventStartTime <= :startTime AND eventEndTime >= :endTime))";
    
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
            return $count > 0;
        } catch (PDOException $e) {
            // Handle the exception
            error_log("Error checking event overlap: " . $e->getMessage());
            return false;
        }
    }
    
    

    function getEventFacilitators($eventID)
    {
        $sql = "SELECT librarian.* FROM event_facilitator 
                JOIN librarian ON event_facilitator.librarianID = librarian.librarianID 
                WHERE event_facilitator.eventID = :eventID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventID', $eventID);
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

    // function getEventDateTimes($eventID)
    // {
    //     $sql = "SELECT eventDate, eventStartTime, eventEndTime FROM event_datetime WHERE eventID = :eventID";
    //     $query = $this->db->connect()->prepare($sql);
    //     $query->bindParam(':eventID', $eventID);
    //     if ($query->execute()) {
    //         $data = $query->fetchAll();
    //     }
    //     return $data;
    // }


    function show()
    {
        $sql = "SELECT * FROM event ORDER BY eventCreatedAt DESC;";
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

    public function getEventImages($eventID)
    {
        $sql = "SELECT * FROM event_images WHERE eventID = :eventID";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':eventID', $eventID);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getFeedback($userID, $eventID) {
        $sql = "SELECT * FROM event_feedback WHERE userID = :userID AND eventID = :eventID";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':eventID', $eventID);
        $stmt->execute();
        $feedback = $stmt->fetch(PDO::FETCH_ASSOC);
        return $feedback;
    }

    public function hasFeedback($userID, $eventID) {
        $sql = "SELECT COUNT(*) FROM event_feedback WHERE userID = :userID AND eventID = :eventID";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':eventID', $eventID);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count > 0;
    }

    public function checkFeedback($userID, $eventID) {
        $query = "SELECT * FROM event_feedback WHERE userID = :userID AND eventID = :eventID";
        $stmt =  $this->db->connect()->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':eventID', $eventID);
        $stmt->execute();
    
        // Check if the user has submitted feedback
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function getCertificateImage($eventID, $userID) {
        $sql = "SELECT ecImage FROM event_certificate WHERE eventID = :eventID AND userID = :userID";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':eventID', $eventID);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $certificateImage = $stmt->fetchColumn(); // Fetch the first column directly
    
        // Remove the `../../` prefix from the image path
        if ($certificateImage !== false) {
            $certificateImage = str_replace('', '', $certificateImage);
        }
    
        return $certificateImage !== false ? $certificateImage : ""; // Return certificate image URL or empty string if not found
    }

    public function checkUserCertificate($userID, $eventID) {
        $query = "SELECT * FROM event_certificate WHERE userID = :userID AND eventID = :eventID";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':eventID', $eventID);
        $stmt->execute();

        // Check if the user has a certificate
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function submitFeedback($userID, $eventID, $ratings, $feedback) {
        $query = "INSERT INTO event_feedback (userID, eventID, ratings, feedback) VALUES (:userID, :eventID, :ratings, :feedback)";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':eventID', $eventID);
        $stmt->bindParam(':ratings', $ratings);
        $stmt->bindParam(':feedback', $feedback);
        $stmt->execute();
        return $stmt->rowCount();
    }
    
    function showClubEvents($clubID)
    {
        $sql = "SELECT e.* FROM event e 
                INNER JOIN club_event ce ON e.eventID = ce.eventID 
                WHERE ce.clubID = :clubID 
                ORDER BY e.eventCreatedAt DESC;";
        
        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute(['clubID' => $clubID])) {
            $data = $query->fetchAll();
        }
        return $data ?: []; // Return an empty array if $data is falsy
    }

    
    
}

?>
