<?php

require_once ('database.php');

class Account{

    public $librarianID;
    public $librarianEmail;
    public $librarianPassword;

    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function sign_in_users(){
        $sql = "SELECT * FROM librarian WHERE librarianEmail = :librarianEmail LIMIT 1;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':librarianEmail', $this->librarianEmail);
        
        if ($query->execute()) {
            $accountData = $query->fetch(PDO::FETCH_ASSOC);
        
            if ($accountData && password_verify($this->librarianPassword, $accountData['librarianPassword']) && $accountData['account_activation_hash'] === NULL) {
                $this->librarianID = $accountData['librarianID']; // Store librarianID in the class property
                $this->librarianEmail = $accountData['librarianEmail']; // Store librarianEmail in the class property
    
                return true;
            }
        }
        
        return false;
    }
    
    

}
?>
