<?php
require_once '../config/database.php';

class Task {
    private $conn;
    private $table = 'tasks';
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    // Create task
    public function create($client_id, $title, $description, $category, $budget, $deadline) {
        $query = "INSERT INTO " . $this->table . " 
                  (client_id, title, description, category, budget, deadline) 
                  VALUES (:client_id, :title, :description, :category, :budget, :deadline)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':client_id', $client_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':budget', $budget);
        $stmt->bindParam(':deadline', $deadline);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    // Get all tasks with optional filters
    public function getAllTasks($category = null, $budget_order = null, $deadline_filter = null) {
        $query = "SELECT t.*, u.full_name as client_name 
                  FROM " . $this->table . " t 
                  JOIN users u ON t.client_id = u.user_id 
                  WHERE t.status = 'open'";
        
        if($category) {
            $query .= " AND t.category = :category";
        }
        
        if($deadline_filter) {
            $query .= " AND t.deadline <= :deadline_filter";
        }
        
        if($budget_order == 'low_to_high') {
            $query .= " ORDER BY t.budget ASC";
        } elseif($budget_order == 'high_to_low') {
            $query .= " ORDER BY t.budget DESC";
        } else {
            $query .= " ORDER BY t.date_posted DESC";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if($category) {
            $stmt->bindParam(':category', $category);
        }
        
        if($deadline_filter) {
            $stmt->bindParam(':deadline_filter', $deadline_filter);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Get task by ID
    public function getTaskById($task_id) {
        $query = "SELECT t.*, u.full_name as client_name 
                  FROM " . $this->table . " t 
                  JOIN users u ON t.client_id = u.user_id 
                  WHERE t.task_id = :task_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Get tasks by user ID (for dashboard)
    public function getTasksByUserId($user_id, $role) {
        if($role == 'freelancer') {
            $query = "SELECT t.*, ta.status as assignment_status 
                      FROM task_assignments ta 
                      JOIN " . $this->table . " t ON ta.task_id = t.task_id 
                      WHERE ta.freelancer_id = :user_id 
                      ORDER BY ta.start_date DESC";
        } else {
            $query = "SELECT * FROM " . $this->table . " 
                      WHERE client_id = :user_id 
                      ORDER BY date_posted DESC";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Update task status
    public function updateStatus($task_id, $status) {
        $query = "UPDATE " . $this->table . " 
                  SET status = :status, date_updated = CURRENT_TIMESTAMP 
                  WHERE task_id = :task_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':task_id', $task_id);
        
        return $stmt->execute();
    }
    
    // Update proposals count
    public function updateProposalsCount($task_id) {
        $query = "UPDATE " . $this->table . " 
                  SET proposals_count = (
                      SELECT COUNT(*) FROM proposals WHERE task_id = :task_id
                  ) 
                  WHERE task_id = :task_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':task_id', $task_id);
        
        return $stmt->execute();
    }
    
    // Get task statistics
    public function getTaskStats($user_id, $role) {
        if($role == 'freelancer') {
            $query = "SELECT 
                        COUNT(CASE WHEN ta.status = 'completed' THEN 1 END) as completed,
                        COUNT(CASE WHEN ta.status = 'in_progress' THEN 1 END) as in_progress,
                        COUNT(CASE WHEN ta.status = 'submitted' THEN 1 END) as submitted
                      FROM task_assignments ta 
                      WHERE ta.freelancer_id = :user_id";
        } else {
            $query = "SELECT 
                        COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
                        COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress,
                        COUNT(CASE WHEN status = 'open' THEN 1 END) as open
                      FROM " . $this->table . " 
                      WHERE client_id = :user_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Delete task
    public function delete($task_id) {
        $query = "DELETE FROM " . $this->table . " WHERE task_id = :task_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':task_id', $task_id);
        
        return $stmt->execute();
    }
}
?>