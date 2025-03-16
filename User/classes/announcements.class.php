<?php

require_once 'database.php';

class Announcements{
    //attributes

    public $eventAnnouncementID;
    public $eaTitle;
    public $eaDescription;
    public $eaStartDate;
    public $eaEndDate;
    public $eaStartTime;
    public $eaEndTime;


    protected $db;
    
    function __construct()
    {
        $this->db = new Database();
    }

    //Methods
    function showAnnouncements()
    {
        $sql = "SELECT * FROM event_announcement ORDER BY eaCreatedAt DESC;";
        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data ?: []; // Return an empty array if $data is falsy
    }


}