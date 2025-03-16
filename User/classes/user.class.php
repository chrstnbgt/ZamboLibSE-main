<?php

require_once 'database.php';

class User
{
    public $userID;
    public $userFirstName;
    public $userMiddleName;
    public $userLastName;
    public $userEmail;
    public $userPassword;
    public $account_activation_hash;

    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    public function signup($conn)
    {
        // Generate activation token
        $activation_token = bin2hex(random_bytes(16));
        $activation_token_hash = hash("sha256", $activation_token);

        $sql = "INSERT INTO user (userFirstName, userMiddleName, userLastName, userEmail, userPassword, account_activation_hash) VALUES 
        (:userFirstName, :userMiddleName, :userLastName, :userEmail, :userPassword, :activation_token_hash);";

        $query = $conn->prepare($sql);
        $query->bindParam(':userFirstName', $this->userFirstName);
        $query->bindParam(':userMiddleName', $this->userMiddleName);
        $query->bindParam(':userLastName', $this->userLastName);
        $query->bindParam(':userEmail', $this->userEmail);
        // Hash the password securely using password_hash
        $hashedPassword = password_hash($this->userPassword, PASSWORD_DEFAULT);
        $query->bindParam(':userPassword', $hashedPassword);
        $query->bindParam(':activation_token_hash', $activation_token_hash);

        if ($query->execute()) {
            // Set userID to the last inserted ID
            $this->userID = $conn->lastInsertId();
            return $activation_token;
        } else {
            return false;
        }
    }

    public function setAccountActivationHash($activation_token, $conn)
    {
        $activation_token_hash = hash("sha256", $activation_token);
        $sql = "UPDATE user SET account_activation_hash = :activation_token_hash WHERE userID = :userID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":activation_token_hash", $activation_token_hash, PDO::PARAM_STR);
        $stmt->bindParam(":userID", $this->userID, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    

    function isUserNameExist()
    {
        $sql = "SELECT * FROM user WHERE userUserName = :userUserName;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':userUserName', $this->userUserName);
        if ($query->execute()) {
            if ($query->rowCount() > 0) {
                return true;
            }
        }
        return false;
    }

    function checkPassword()
    {
        $sql = "SELECT userPassword FROM user WHERE userID = :userID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':userID', $this->userID);
        $query->execute();

        // Check if the user exists
        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $hashedPassword = $row['userPassword'];

            // Verify the password
            if (password_verify($this->userPassword, $hashedPassword)) {
                return true;
            }
        }
        return false;
    }

    public function getTotalUsers()
    {
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare("SELECT COUNT(*) AS totalUsers FROM user");
            $stmt->execute();
            $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['totalUsers'];
            return $totalUsers;
        } catch (PDOException $e) {
            error_log("Error getting total number of users: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalClubs()
    {
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare("SELECT COUNT(*) AS totalClubs FROM club");
            $stmt->execute();
            $totalClubs = $stmt->fetch(PDO::FETCH_ASSOC)['totalClubs'];
            return $totalClubs;
        } catch (PDOException $e) {
            error_log("Error getting total number of users: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalEvents()
    {
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare("SELECT COUNT(*) AS totalEvents FROM event");
            $stmt->execute();
            $totalEvents = $stmt->fetch(PDO::FETCH_ASSOC)['totalEvents'];
            return $totalEvents;
        } catch (PDOException $e) {
            error_log("Error getting total number of users: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalIncomingEvents()
{
    try {
        $conn = $this->db->connect();
        $currentDate = date("Y-m-d"); // Get current date
        $stmt = $conn->prepare("SELECT COUNT(*) AS totalIncomingEvents FROM event WHERE eventStartDate > :currentDate");
        $stmt->bindParam(':currentDate', $currentDate);
        $stmt->execute();
        $totalIncomingEvents = $stmt->fetch(PDO::FETCH_ASSOC)['totalIncomingEvents'];
        return $totalIncomingEvents;
    } catch (PDOException $e) {
        error_log("Error getting total number of incoming events: " . $e->getMessage());
        return 0;
    }
}

}
?>
