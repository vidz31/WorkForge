<?php
require_once '../config/database.php';

class User {
    private $conn;
    private $table = 'users';
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    // Create user (Registration)
    public function create($full_name, $email, $password, $role) {
        $query = "INSERT INTO " . $this->table . " 
                  (full_name, email, password, role) 
                  VALUES (:full_name, :email, :password, :role)";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    // Login user
    public function login($email, $password) {
        $query = "SELECT user_id, full_name, email, password, role, profile_picture, 
                         average_rating, completed_tasks 
                  FROM " . $this->table . " 
                  WHERE email = :email AND is_active = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            
            if(password_verify($password, $user['password'])) {
                // Update last login
                $this->updateLastLogin($user['user_id']);
                
                // Remove password from returned data
                unset($user['password']);
                return $user;
            }
        }
        return false;
    }
    
    // Get user by ID
    public function getUserById($user_id) {
        $query = "SELECT user_id, full_name, email, role, profile_picture, 
                         average_rating, completed_tasks, date_created 
                  FROM " . $this->table . " 
                  WHERE user_id = :user_id AND is_active = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Update user profile
    public function updateProfile($user_id, $full_name, $email, $profile_picture = null) {
        $query = "UPDATE " . $this->table . " 
                  SET full_name = :full_name, email = :email";
        
        if($profile_picture) {
            $query .= ", profile_picture = :profile_picture";
        }
        
        $query .= " WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':user_id', $user_id);
        
        if($profile_picture) {
            $stmt->bindParam(':profile_picture', $profile_picture);
        }
        
        return $stmt->execute();
    }
    
    // Update user stats
    public function updateStats($user_id, $completed_tasks, $average_rating) {
        $query = "UPDATE " . $this->table . " 
                  SET completed_tasks = :completed_tasks, average_rating = :average_rating 
                  WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':completed_tasks', $completed_tasks);
        $stmt->bindParam(':average_rating', $average_rating);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }
    
    // Update last login
    private function updateLastLogin($user_id) {
        $query = "UPDATE " . $this->table . " 
                  SET last_login = CURRENT_TIMESTAMP 
                  WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    }
    
    // Check if email exists
    public function emailExists($email) {
        $query = "SELECT user_id FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    // Delete user
    public function delete($user_id) {
        $query = "UPDATE " . $this->table . " 
                  SET is_active = 0 
                  WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }
}
?>