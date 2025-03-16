<?php
require_once 'database.php';

class ClubForm {
    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    public function fetchClubFormQuestions($clubID) {
        try {
            $stmt = $this->db->connect()->prepare("
                SELECT clubFormQuestionID, cfQuestion
                FROM club_formquestion
                WHERE clubID = :clubID");
            $stmt->bindParam(':clubID', $clubID, PDO::PARAM_INT);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            echo "Error fetching club form questions: " . $e->getMessage();
            return [];
        }
    }

    public function getClubMembershipID($clubID, $userID) {
        try {
            $stmt = $this->db->connect()->prepare("SELECT clubMembershipID FROM club_membership WHERE clubID = :clubID AND userID = :userID");
            $stmt->bindParam(':clubID', $clubID, PDO::PARAM_INT);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
    
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($result && isset($result['clubMembershipID'])) {
                return $result['clubMembershipID'];
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error fetching club membership ID: " . $e->getMessage();
            return false;
        }
    }
    
    public function fetchClubRegistrationAnswers($clubID, $clubMembershipID, $userID) {
        try {
            $stmt = $this->db->connect()->prepare("
                SELECT cfa.cfAnswer, cfq.cfQuestion
                FROM club_formanswer cfa
                INNER JOIN club_formquestion cfq ON cfa.clubFormQuestionID = cfq.clubFormQuestionID
                INNER JOIN club_membership cm ON cfa.clubMembershipID = cm.clubMembershipID
                WHERE cfq.clubID = :clubID AND cm.userID = :userID AND cfa.clubMembershipID = :clubMembershipID");
            $stmt->bindParam(':clubID', $clubID, PDO::PARAM_INT);
            $stmt->bindParam(':clubMembershipID', $clubMembershipID, PDO::PARAM_INT);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            echo "Error fetching club registration answers: " . $e->getMessage();
            return false;
        }
    }
    
    
    public function fetchClubFormAnswers($clubMembershipID) {
        try {
            $stmt = $this->db->connect()->prepare("
                SELECT cfa.clubFormQuestionID, cfq.cfQuestion, cfa.cfAnswer 
                FROM club_formanswer cfa
                INNER JOIN club_formquestion cfq ON cfa.clubFormQuestionID = cfq.clubFormQuestionID
                WHERE cfa.clubMembershipID = :clubMembershipID");
            $stmt->bindParam(':clubMembershipID', $clubMembershipID, PDO::PARAM_INT);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            echo "Error fetching club form answers: " . $e->getMessage();
            return [];
        }
    }
    
    public function insertClubFormAnswers($clubID, $userID, $answers) {
        try {
            // Get club membership ID
            $clubMembershipID = $this->getClubMembershipID($clubID, $userID);
    
            // If user is not a member, insert club membership first
            if (!$clubMembershipID) {
                $clubMembershipID = $this->insertClubMembership($clubID, $userID);
            }

            $clubMembershipID = $this->getClubMembershipID($clubID, $userID);
    
                // Prepare the insert statement
                $stmt = $this->db->connect()->prepare("INSERT INTO club_formanswer (clubFormQuestionID, clubMembershipID, cfAnswer) VALUES (:clubFormQuestionID, :clubMembershipID, :cfAnswer)");
                
                // Bind parameters and execute for each answer
                foreach ($answers as $questionID => $answer) {
                    $stmt->bindParam(':clubFormQuestionID', $questionID, PDO::PARAM_INT);
                    $stmt->bindParam(':clubMembershipID', $clubMembershipID, PDO::PARAM_INT);
                    $stmt->bindParam(':cfAnswer', $answer, PDO::PARAM_STR);
                    
                    // Execute the statement
                    $stmt->execute();
                }
    
                return true;

        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            echo "Error inserting club form answers: " . $e->getMessage();
            return false;
        }
    }
    
    
    
    public function isUserMemberOfClub($clubID, $userID) {
        try {
            $stmt = $this->db->connect()->prepare("SELECT COUNT(*) AS count FROM club_membership WHERE clubID = :clubID AND userID = :userID");
            $stmt->bindParam(':clubID', $clubID, PDO::PARAM_INT);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['count'] > 0;
        } catch (PDOException $e) {
            echo "Error checking if user is member of club: " . $e->getMessage();
            return false;
        }
    }

    public function fetch($clubID) {
        try {
            $stmt = $this->db->connect()->prepare("SELECT * FROM club WHERE clubID = :clubID");
            $stmt->bindParam(':clubID', $clubID, PDO::PARAM_INT);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($result)) {
                return false; // Club not found
            }
    
            return $result[0];
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            echo "Error fetching club: " . $e->getMessage();
            return false;
        }
    }

    public function insertClubMembership($clubID, $userID) {
        try {
            $stmt = $this->db->connect()->prepare("INSERT INTO club_membership (clubID, userID) VALUES (:clubID, :userID)");
            $stmt->bindParam(':clubID', $clubID, PDO::PARAM_INT);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();

            $clubMembershipID = $this->db->connect()->lastInsertId();
            echo "ITO: " . $clubMembershipID;
            return $clubMembershipID;
        } catch (PDOException $e) {
            echo "Error inserting club membership: " . $e->getMessage();
            return false;
        }
    }
}
?>