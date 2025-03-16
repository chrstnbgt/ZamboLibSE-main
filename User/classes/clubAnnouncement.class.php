<?php

require_once 'database.php';

class ClubAnnouncement {
    private $db;

    public $clubAnnouncementID;
    public $clubID;
    public $caTitle;
    public $caDescription;
    public $caCondition;
    public $caDate;
    public $caTime;


    public function __construct() {
        $this->db = new Database();
    }

    public function fetchAnnouncements($clubID) {
        try {
            $sql = "SELECT * FROM club_announcement WHERE clubID = :clubID";
            $query = $this->db->connect()->prepare($sql);
            $query->bindParam(':clubID', $clubID, PDO::PARAM_INT);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            error_log("Error fetching club announcements: " . $e->getMessage());
            return [];
        }
    }
    

    function show()
    {
        $sql = "SELECT * FROM club ORDER BY clubName ASC;";
        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data ?: []; // Return an empty array if $data is falsy
    }
    


    function getAllClubs() {
        $sql = "SELECT * FROM club";
        $query = $this->db->connect()->prepare($sql);
    
        $clubs = null;
    
        if ($query->execute()) {
            $clubs = $query->fetchAll();
        }
    
        return $clubs;
    }
}
