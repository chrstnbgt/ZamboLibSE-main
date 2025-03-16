<?php

require_once 'database.php';

class OrganizationProposal {
    //attributes

    public $proposalID;
    public $proposalSubject;
    public $proposalDescription;
    public $proposalFile;
    public $organizationClubID;
    protected $db;
    
    function __construct()
    {
        $this->db = new Database();
    }
    function addProposalWithOrgProposal($organizationClubID, $proposalSubject, $proposalDescription, $proposalFile) {
        try {
            // Store connection and begin transaction
            $db = $this->db->connect();
            $db->beginTransaction();
    
            // Add proposal to the `proposal` table
            $sql = "INSERT INTO proposal (proposalSubject, proposalDescription) VALUES (:proposalSubject, :proposalDescription)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':proposalSubject', $proposalSubject, PDO::PARAM_STR);
            $stmt->bindParam(':proposalDescription', $proposalDescription, PDO::PARAM_STR);
            $stmt->execute();
    
            // Get the last inserted `proposalID`
            $proposalID = $db->lastInsertId();
    
            // Add proposal file to the `proposal_files` table
            $sql = "INSERT INTO proposal_files (proposalID, proposalFile) VALUES (:proposalID, :proposalFile)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':proposalID', $proposalID, PDO::PARAM_INT);
            $stmt->bindParam(':proposalFile', $proposalFile, PDO::PARAM_STR);
            $stmt->execute();
    
            // Add org proposal to the `org_proposal` table
            $sql = "INSERT INTO org_proposal (organizationClubID, proposalID, status) VALUES (:organizationClubID, :proposalID, 'Pending')";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':organizationClubID', $organizationClubID, PDO::PARAM_INT);
            $stmt->bindParam(':proposalID', $proposalID, PDO::PARAM_INT);
            $stmt->execute();
    
            // Commit transaction
            $db->commit();
            return true;
        } catch (Exception $e) {
            // Rollback transaction on failure
            $db->rollBack();
            throw $e;
        }
    }
    
    
    
    

    //Methods
    public function fetchOrganizationClubs($userID) {
        try {
            $stmt = $this->db->connect()->prepare("SELECT * FROM organization_club WHERE userID = :userID");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($result)) {
                return false; // No organization clubs found for the user
            }
    
            return $result;
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            echo "Error fetching organization clubs: " . $e->getMessage();
            return false;
        }
    }
    
    
    
    


    function show($organizationClubID)
    {
        try {
            $sql = "SELECT p.*, op.status
                    FROM proposal p 
                    JOIN org_proposal op ON p.proposalID = op.proposalID
                    WHERE op.organizationClubID = :organizationClubID
                    ORDER BY p.proposalCreatedAt ASC;";
            $query = $this->db->connect()->prepare($sql);
            $query->bindParam(':organizationClubID', $organizationClubID, PDO::PARAM_INT);
            $data = [];
    
            if ($query->execute()) {
                $data = $query->fetchAll();
            }
            return $data;
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            error_log("Error fetching proposals: " . $e->getMessage());
            return [];
        }
    }

    public function delete($proposalID)
    {
        try {
            $sql = "DELETE FROM proposal WHERE proposalID = :proposalID";
            $query = $this->db->connect()->prepare($sql);
            $query->bindParam(':proposalID', $proposalID, PDO::PARAM_INT);
            
            // Execute the query
            return $query->execute();
        } catch (PDOException $e) {
            // Handle the exception, e.g., log the error or show a user-friendly message
            error_log("Error deleting proposal: " . $e->getMessage());
            return false;
        }
    }

    
    
}

