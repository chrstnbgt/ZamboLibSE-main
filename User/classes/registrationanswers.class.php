<?php

require_once 'database.php';

class Event_reganswer {
    public $eventRegAnswerID;
    public $eventRegQuestionID;
    public $eventRegistrationID;
    public $erAnswer;

    protected $db;

    function __construct() {
        $this->db = new Database();
    }

    public function addAnswers3() {
        $conn = $this->db->connect(); // Get the database connection from the Database class
    
        $stmt = $conn->prepare("INSERT INTO event_reganswer (eventRegAnswerID, eventRegQuestionID, eventRegistrationID, erAnswer) VALUES 
        (:eventRegAnswerID, :eventRegQuestionID, :eventRegistrationID, :erAnswer)");
    

        $stmt->bindParam(':eventRegAnswerID', $this->eventRegAnswerID);
        $stmt->bindParam(':eventRegQuestionID', $this->eventRegQuestionID);
        $stmt->bindParam(':eventRegistrationID', $this->eventRegistrationID);
        $stmt->bindParam(':erAnswer', $this->erAnswer);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function addAnswers(){
        $sql = "INSERT INTO event_reganswer (eventRegAnswerID, eventRegQuestionID, eventRegistrationID, erAnswer) VALUES 
        (:eventRegAnswerID, :eventRegQuestionID, :eventRegistrationID, :erAnswer);";

        $query=$this->db->connect()->prepare($sql);
        $query->bindParam(':eventRegAnswerID', $this->eventRegAnswerID);
        $query->bindParam(':eventRegQuestionID', $this->eventRegQuestionID);
        $query->bindParam(':eventRegistrationID', $this->eventRegistrationID);
        $query->bindParam(':erAnswer', $this->erAnswer);
        
        if($query->execute()){
            return true;
        }
        else{
            return false;
        }	
    }
}

// RegistrationAnswers class definition
class RegistrationAnswers {
    protected $db;

    function __construct() {
        $this->db = new Database();
    }

    public function addEventRegistrationForm($eventID, $formData) {
        // Loop through the form data and insert each answer into the database
        foreach ($formData as $data) {
            if (isset($data['eventRegQuestionID'], $data['eventRegistrationID'], $data['erAnswer'])) {
                $answer = new Event_reganswer(); // Assuming Event_reganswer is a valid class
                $answer->eventRegQuestionID = $data['eventRegQuestionID'];
                $answer->eventRegistrationID = $data['eventRegistrationID'];
                $answer->erAnswer = $data['erAnswer'];
                $answer->addAnswers(); // Call the addAnswers method to insert the answer into the database
            } else {
                // Handle missing or invalid data
                return false;
            }
        }
        return true;
    }
}

?>
