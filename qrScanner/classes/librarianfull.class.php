<?php

require_once 'database.php';

class Librarian {
    public $librarianID;
    public $librarianFirstName;
    public $librarianMiddleName;
    public $librarianLastName;
    public $librarianDesignation;
    public $librarianContactNo;
    public $librarianEmail;
    public $librarianPassword;
    public $librarianImage;
    public $librarianEmployment;

    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function signIn()
    {
        $sql = "SELECT * FROM librarian WHERE librarianEmail = :librarianEmail LIMIT 1;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':librarianEmail', $this->librarianEmail);
    
        if ($query->execute()) {
            $accountData = $query->fetch(PDO::FETCH_ASSOC);
    
            if ($accountData && password_verify($this->librarianPassword, $accountData['librarianPassword'])) {
                $this->librarianID = $accountData['librarianID'];
                return true;
            }
        }
    
        return false;
    }

    function fetch($librarianID)
    {
        $sql = "SELECT * FROM librarian WHERE librarianID = :librarianID;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':librarianID', $librarianID);
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }

    function show()
    {
        $sql = "SELECT * FROM user ORDER BY userLastName ASC, userFirstName ASC;";
        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }

    function isEmailExist()
    {
        $sql = "SELECT * FROM user WHERE userEmail = :userEmail;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':userEmail', $this->userEmail);
        if ($query->execute()) {
            if ($query->rowCount() > 0) {
                return true;
            }
        }
        return false;
    }


    function checkPassword() {
        $sql = "SELECT userPassword FROM user WHERE userID = :userID";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':userID', $this->userID);
        $query->execute();

        // Check if the user exists
        if ($query->rowCount() > 0) {
            $hashedPassword = $query->fetchColumn();
            // Use password_verify to check if the entered password matches the hashed password
            return password_verify($this->userPassword, $hashedPassword);
        } else {
            return false; // user not found
        }
    }

}

?>
