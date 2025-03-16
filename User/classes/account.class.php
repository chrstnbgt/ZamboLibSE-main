<?php

require_once ('database.php');

class Account{

    public $userID;
    public $userEmail;
    public $userPassword;

    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function sign_in_users(){
        $sql = "SELECT * FROM user WHERE userEmail = :userEmail LIMIT 1;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':userEmail', $this->userEmail);
        
        if ($query->execute()) {
            $accountData = $query->fetch(PDO::FETCH_ASSOC);
        
            if ($accountData && password_verify($this->userPassword, $accountData['userPassword']) && $accountData['account_activation_hash'] === NULL) {
                $this->userID = $accountData['userID']; // Store userID in the class property
                $this->userEmail = $accountData['userEmail']; // Store userEmail in the class property
    
                return true;
            }
        }
        
        return false;
    }
    
    

}
