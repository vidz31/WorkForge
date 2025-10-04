<?php
require_once '../config/database.php';

class Proposal {
    private $conn;
    private $table = 'proposals';
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    // Create proposal
    public function create($task_id, $freelancer_id, $bid_amount, $cover_message) {
        // Check if user already submitted a proposal for this task
        if($this->hasUserProposed($task_id, $freelancer_id)) {
            return ['success' => false, 'message' => 'You have already submitted a proposal for this task.'];
        }
        
        $query = "INSERT INTO " . $this->table . " 
                  (task_id, freelancer_id, bid_amount, cover_message) 
                  VALUES (:task_id, :freelancer_id, :bid_amount, :cover_message)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':task_id', $task_id);
        $stmt->bindParam(':freelancer_id', $freelancer_id);
        $stmt->bindParam(':bid_amount', $bid_amount);
        $stmt->bindParam(':cover_message', $cover_message);
        
        if($stmt->execute()) {
            // Update task proposals count
            $this->updateTaskProposalsCount($task_id);
            return ['success' => true, 'proposal_id' => $this->conn->lastInsertId()];
        }
        return ['success' => false, 'message' => 'Failed to submit proposal.'];
    }
    
    // Get proposals for a task
    public function getProposalsByTask($task_id) {
        $query = "SELECT p.*, u.full_name, u.profile_picture, u.average_rating, u.completed_tasks 
                  FROM " . $this->table . " p 
                  JOIN users u ON p.freelancer_id = u.user_id 
                  WHERE p.task_id = :task_id 
                  ORDER BY p.date_submitted DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Get proposals by freelancer
    public function getProposalsByFreelancer($freelancer_id) {
        $query = "SELECT p.*, t.title, t.budget, t.deadline, t.status as task_status 
                  FROM " . $this->table . " p 
                  JOIN tasks t ON p.task_id = t.task_id 
                  WHERE p.freelancer_id = :freelancer_id 
                  ORDER BY p.date_submitted DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':freelancer_id', $freelancer_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Get proposal by ID
    public function getProposalById($proposal_id) {
        $query = "SELECT p.*, t.title, t.description, t.budget, t.deadline, 
                         u.full_name as freelancer_name, u.profile_picture, u.average_rating 
                  FROM " . $this->table . " p 
                  JOIN tasks t ON p.task_id = t.task_id 
                  JOIN users u ON p.freelancer_id = u.user_id 
                  WHERE p.proposal_id = :proposal_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':proposal_id', $proposal_id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Update proposal status
    public function updateStatus($proposal_id, $status) {
        $query = "UPDATE " . $this->table . " 
                  SET status = :status 
                  WHERE proposal_id = :proposal_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':proposal_id', $proposal_id);
        
        return $stmt->execute();
    }
    
    // Accept proposal (and assign task)
    public function acceptProposal($proposal_id) {
        try {
            $this->conn->beginTransaction();
            
            // Get proposal details
            $proposal = $this->getProposalById($proposal_id);
            if(!$proposal) {
                throw new Exception("Proposal not found");
            }
            
            // Update proposal status to accepted
            $this->updateStatus($proposal_id, 'accepted');
            
            // Reject other proposals for this task
            $this->rejectOtherProposals($proposal['task_id'], $proposal_id);
            
            // Create task assignment
            $assignment_query = "INSERT INTO task_assignments 
                                (task_id, freelancer_id, proposal_id, status) 
                                VALUES (:task_id, :freelancer_id, :proposal_id, 'assigned')";
            
            $assignment_stmt = $this->conn->prepare($assignment_query);
            $assignment_stmt->bindParam(':task_id', $proposal['task_id']);
            $assignment_stmt->bindParam(':freelancer_id', $proposal['freelancer_id']);
            $assignment_stmt->bindParam(':proposal_id', $proposal_id);
            $assignment_stmt->execute();
            
            // Update task status
            $task_query = "UPDATE tasks SET status = 'in_progress' WHERE task_id = :task_id";
            $task_stmt = $this->conn->prepare($task_query);
            $task_stmt->bindParam(':task_id', $proposal['task_id']);
            $task_stmt->execute();
            
            $this->conn->commit();
            return ['success' => true, 'message' => 'Proposal accepted successfully'];
            
        } catch(Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Check if user has already proposed for a task
    private function hasUserProposed($task_id, $freelancer_id) {
        $query = "SELECT proposal_id FROM " . $this->table . " 
                  WHERE task_id = :task_id AND freelancer_id = :freelancer_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->bindParam(':freelancer_id', $freelancer_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    // Update task proposals count
    private function updateTaskProposalsCount($task_id) {
        $query = "UPDATE tasks 
                  SET proposals_count = (
                      SELECT COUNT(*) FROM " . $this->table . " WHERE task_id = :task_id
                  ) 
                  WHERE task_id = :task_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();
    }
    
    // Reject other proposals for a task
    private function rejectOtherProposals($task_id, $accepted_proposal_id) {
        $query = "UPDATE " . $this->table . " 
                  SET status = 'rejected' 
                  WHERE task_id = :task_id AND proposal_id != :accepted_proposal_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->bindParam(':accepted_proposal_id', $accepted_proposal_id);
        $stmt->execute();
    }
    
    // Delete proposal
    public function delete($proposal_id) {
        $query = "DELETE FROM " . $this->table . " WHERE proposal_id = :proposal_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':proposal_id', $proposal_id);
        
        return $stmt->execute();
    }
}
?>