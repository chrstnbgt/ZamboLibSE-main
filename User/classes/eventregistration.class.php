<?php

require_once 'database.php';

class EventRegistrationForm
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getEventRegistrationForm($eventID)
    {
        $sql = "SELECT e.*, q.*, a.*
                FROM event_registration e
                LEFT JOIN event_regquestion q ON e.eventRegistrationID = q.eventRegistrationID
                LEFT JOIN event_reganswer a ON q.eventRegQuestionID = a.eventRegQuestionID AND e.eventRegistrationID = a.eventRegistrationID
                WHERE e.eventID = :eventID";
                
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':eventID', $eventID);
        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $data = []; // Return an empty array if query fails
        }
        return $data;
    }

    public function addEventRegistrationForm($eventID, $formData)
    {
        // Prepare SQL statement to insert data into event_registration table
        $registrationSQL = "INSERT INTO event_registration (eventID) VALUES (:eventID)";
        $registrationQuery = $this->db->connect()->prepare($registrationSQL);
        $registrationQuery->bindParam(':eventID', $eventID);
        $registrationQuery->execute();

        // Get the last inserted event registration ID
        $lastEventRegistrationID = $this->db->connect()->lastInsertId();

        // Prepare SQL statement to insert form data into event_reganswer table
        $answerSQL = "INSERT INTO event_reganswer (eventRegistrationID, eventRegQuestionID, answer) VALUES (:eventRegistrationID, :eventRegQuestionID, :answer)";
        $answerQuery = $this->db->connect()->prepare($answerSQL);

        // Insert form data for each question
        foreach ($formData as $questionID => $answer) {
            $answerQuery->bindParam(':eventRegistrationID', $lastEventRegistrationID);
            $answerQuery->bindParam(':eventRegQuestionID', $questionID);
            $answerQuery->bindParam(':answer', $answer);
            $answerQuery->execute();
        }
    }

}

?>
