<?php
require_once 'database.php';

class EventForm {
    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    public function fetchEventRegistrationQuestions($eventID, $userID) {
        try {
            $stmt = $this->db->connect()->prepare("
                SELECT erq.eventRegQuestionID, erq.erQuestion, era.erAnswer 
                FROM event_regform erf 
                INNER JOIN event_regquestion erq ON erf.eventRegistrationFormID = erq.eventRegistrationFormID 
                LEFT JOIN event_reganswer era ON erq.eventRegQuestionID = era.eventRegQuestionID 
                WHERE erf.eventID = :eventID");
            $stmt->bindParam(':eventID', $eventID, PDO::PARAM_INT);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            echo "Error fetching event registration questions: " . $e->getMessage();
            return [];
        }
    }
    
    public function fetchEventRegistrationAnswers($eventID, $userID) {
        try {
            $stmt = $this->db->connect()->prepare("
                SELECT erq.eventRegQuestionID, erq.erQuestion, era.erAnswer 
                FROM event_registration er
                INNER JOIN event_regform erf ON er.eventID = erf.eventID
                INNER JOIN event_regquestion erq ON erf.eventRegistrationFormID = erq.eventRegistrationFormID
                LEFT JOIN event_reganswer era ON erq.eventRegQuestionID = era.eventRegQuestionID AND er.eventRegistrationID = era.eventRegistrationID
                WHERE er.eventID = :eventID AND er.userID = :userID");
            $stmt->bindParam(':eventID', $eventID, PDO::PARAM_INT);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            echo "Error fetching event registration answers: " . $e->getMessage();
            return [];
        }
    }
    
    public function insertEventRegistrationAnswers($eventID, $userID, $answers) {
        try {
            // echo $eventID . $userID;
            // Check if the user is already registered
            $eventRegistrationID = $this->getEventRegistrationID($eventID, $userID);
            
            if (!$eventRegistrationID) {
                // If not registered, insert a new registration entry
                $eventRegistrationID = $this->insertEventRegistration($eventID, $userID);
            }

            $eventRegistrationID = $this->getEventRegistrationID($eventID, $userID);
            
            // Prepare the insert statement
            $stmt = $this->db->connect()->prepare("INSERT INTO event_reganswer (eventRegQuestionID, eventRegistrationID, erAnswer) VALUES (:eventRegQuestionID, :eventRegistrationID, :erAnswer)");
    
            // Bind parameters and execute for each answer
            foreach ($answers as $questionID => $answer) {
                $stmt->bindParam(':eventRegQuestionID', $questionID, PDO::PARAM_INT);
                $stmt->bindParam(':eventRegistrationID', $eventRegistrationID, PDO::PARAM_INT);
                $stmt->bindParam(':erAnswer', $answer, PDO::PARAM_STR);
    
                // Execute the statement
                $stmt->execute();
            }
    
            return true;
        } catch (PDOException $e) {
            echo "Error inserting event registration answers: " . $e->getMessage();
            return false;
        }
    }
    

    public function isUserRegistered($eventID, $userID) {
        try {
            $stmt = $this->db->connect()->prepare("SELECT COUNT(*) AS count FROM event_registration WHERE eventID = :eventID AND userID = :userID");
            $stmt->bindParam(':eventID', $eventID, PDO::PARAM_INT);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['count'] > 0;
        } catch (PDOException $e) {
            echo "Error checking if user is registered: " . $e->getMessage();
            return false;
        }
    }

    private function getEventRegistrationID($eventID, $userID) {
        try {
            $stmt = $this->db->connect()->prepare("SELECT eventRegistrationID FROM event_registration WHERE eventID = :eventID AND userID = :userID");
            $stmt->bindParam(':eventID', $eventID, PDO::PARAM_INT);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
    
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($result && isset($result['eventRegistrationID'])) {
                echo "Existing event registration ID: " . $result['eventRegistrationID'] . "<br>";
                return $result['eventRegistrationID'];
            } else {
                // echo "No event registration found, inserting new one...<br>";
                // If no registration entry found, return false
                return false;
            }
        } catch (PDOException $e) {
            echo "Error fetching event registration ID: " . $e->getMessage();
            return false;
        }
    }
    
    private function insertEventRegistration($eventID, $userID) {
        try {
            $stmt = $this->db->connect()->prepare("INSERT INTO event_registration (eventID, userID) VALUES (:eventID, :userID)");
            $stmt->bindParam(':eventID', $eventID, PDO::PARAM_INT);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();

            $eventRegistrationID = $this->db->connect()->lastInsertId();
            // echo "New event registration ID inserted: " . $eventRegistrationID . "<br>";

            // Return the last inserted ID
            return $eventRegistrationID;
        } catch (PDOException $e) {
            echo "Error inserting event registration: " . $e->getMessage();
            return false;
        }
    }
}
?>
