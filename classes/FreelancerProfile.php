<?php
require_once '../config/database.php';

class FreelancerProfile {
    private $conn;
    private $table = 'freelancer_profiles';
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    // Create or update freelancer profile
    public function createOrUpdate($user_id, $skills, $description, $hourly_rate = null, $portfolio_links = null) {
        // Check if profile exists
        if($this->profileExists($user_id)) {
            return $this->updateProfile($user_id, $skills, $description, $hourly_rate, $portfolio_links);
        } else {
            return $this->createProfile($user_id, $skills, $description, $hourly_rate, $portfolio_links);
        }
    }
    
    // Create new profile
    private function createProfile($user_id, $skills, $description, $hourly_rate, $portfolio_links) {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, skills, description, hourly_rate, portfolio_links) 
                  VALUES (:user_id, :skills, :description, :hourly_rate, :portfolio_links)";
        
        $stmt = $this->conn->prepare($query);
        
        // Convert skills array to JSON if needed
        $skills_json = is_array($skills) ? json_encode($skills) : $skills;
        
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':skills', $skills_json);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':hourly_rate', $hourly_rate);
        $stmt->bindParam(':portfolio_links', $portfolio_links);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    // Update existing profile
    private function updateProfile($user_id, $skills, $description, $hourly_rate, $portfolio_links) {
        $query = "UPDATE " . $this->table . " 
                  SET skills = :skills, description = :description, 
                      hourly_rate = :hourly_rate, portfolio_links = :portfolio_links 
                  WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Convert skills array to JSON if needed
        $skills_json = is_array($skills) ? json_encode($skills) : $skills;
        
        $stmt->bindParam(':skills', $skills_json);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':hourly_rate', $hourly_rate);
        $stmt->bindParam(':portfolio_links', $portfolio_links);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }
    
    // Get profile by user ID
    public function getProfileByUserId($user_id) {
        $query = "SELECT fp.*, u.full_name, u.email, u.profile_picture, 
                         u.average_rating, u.completed_tasks, u.date_created 
                  FROM " . $this->table . " fp 
                  JOIN users u ON fp.user_id = u.user_id 
                  WHERE fp.user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $profile = $stmt->fetch();
        
        // Decode skills JSON
        if($profile && $profile['skills']) {
            $profile['skills_array'] = json_decode($profile['skills'], true);
        }
        
        return $profile;
    }
    
    // Get all freelancer profiles with filters
    public function getAllProfiles($skill_filter = null, $rating_min = null, $availability = null) {
        $query = "SELECT fp.*, u.full_name, u.profile_picture, u.average_rating, u.completed_tasks 
                  FROM " . $this->table . " fp 
                  JOIN users u ON fp.user_id = u.user_id 
                  WHERE u.is_active = 1";
        
        if($skill_filter) {
            $query .= " AND fp.skills LIKE :skill_filter";
        }
        
        if($rating_min) {
            $query .= " AND u.average_rating >= :rating_min";
        }
        
        if($availability) {
            $query .= " AND fp.availability = :availability";
        }
        
        $query .= " ORDER BY u.average_rating DESC, u.completed_tasks DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if($skill_filter) {
            $skill_param = "%$skill_filter%";
            $stmt->bindParam(':skill_filter', $skill_param);
        }
        
        if($rating_min) {
            $stmt->bindParam(':rating_min', $rating_min);
        }
        
        if($availability) {
            $stmt->bindParam(':availability', $availability);
        }
        
        $stmt->execute();
        $profiles = $stmt->fetchAll();
        
        // Decode skills JSON for each profile
        foreach($profiles as &$profile) {
            if($profile['skills']) {
                $profile['skills_array'] = json_decode($profile['skills'], true);
            }
        }
        
        return $profiles;
    }
    
    // Update availability
    public function updateAvailability($user_id, $availability) {
        $query = "UPDATE " . $this->table . " 
                  SET availability = :availability 
                  WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':availability', $availability);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }
    
    // Check if profile exists
    private function profileExists($user_id) {
        $query = "SELECT profile_id FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    // Delete profile
    public function delete($user_id) {
        $query = "DELETE FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }
}
?>