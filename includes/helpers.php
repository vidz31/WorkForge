<?php
session_start();

// Authentication helper functions
class Auth {
    
    // Check if user is logged in
    public static function check() {
        return isset($_SESSION['user_id']);
    }
    
    // Get current user data
    public static function user() {
        if(self::check()) {
            return $_SESSION['user_data'];
        }
        return null;
    }
    
    // Get current user ID
    public static function id() {
        if(self::check()) {
            return $_SESSION['user_id'];
        }
        return null;
    }
    
    // Get current user role
    public static function role() {
        if(self::check()) {
            return $_SESSION['user_data']['role'];
        }
        return null;
    }
    
    // Login user
    public static function login($user_data) {
        $_SESSION['user_id'] = $user_data['user_id'];
        $_SESSION['user_data'] = $user_data;
        return true;
    }
    
    // Logout user
    public static function logout() {
        session_unset();
        session_destroy();
        return true;
    }
    
    // Redirect if not authenticated
    public static function requireAuth() {
        if(!self::check()) {
            header('Location: ../html/login.php');
            exit();
        }
    }
    
    // Redirect if not specific role
    public static function requireRole($role) {
        self::requireAuth();
        if(self::role() !== $role) {
            header('Location: ../html/dashboard.php');
            exit();
        }
    }
    
    // Redirect if already authenticated
    public static function guest() {
        if(self::check()) {
            header('Location: ../html/dashboard.php');
            exit();
        }
    }
}

// Validation helper functions
class Validator {
    
    // Validate email
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    // Validate password strength
    public static function password($password) {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number, 1 special character
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
    }
    
    // Validate required field
    public static function required($value) {
        return !empty(trim($value));
    }
    
    // Validate minimum length
    public static function minLength($value, $length) {
        return strlen(trim($value)) >= $length;
    }
    
    // Validate maximum length
    public static function maxLength($value, $length) {
        return strlen(trim($value)) <= $length;
    }
    
    // Validate numeric value
    public static function numeric($value) {
        return is_numeric($value);
    }
    
    // Validate positive number
    public static function positive($value) {
        return is_numeric($value) && $value > 0;
    }
    
    // Validate date format
    public static function date($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}

// Response helper functions
class Response {
    
    // JSON response
    public static function json($data, $status_code = 200) {
        http_response_code($status_code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    // Success response
    public static function success($message, $data = null) {
        $response = ['success' => true, 'message' => $message];
        if($data !== null) {
            $response['data'] = $data;
        }
        self::json($response);
    }
    
    // Error response
    public static function error($message, $status_code = 400) {
        self::json([
            'success' => false,
            'message' => $message
        ], $status_code);
    }
    
    // Redirect
    public static function redirect($url) {
        header("Location: $url");
        exit();
    }
}

// Utility functions
class Utils {
    
    // Sanitize input
    public static function sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    // Generate random token
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    // Upload file
    public static function uploadFile($file, $upload_dir = '../uploads/', $allowed_types = ['jpg', 'jpeg', 'png', 'gif']) {
        if($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload error'];
        }
        
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if(!in_array($file_extension, $allowed_types)) {
            return ['success' => false, 'message' => 'File type not allowed'];
        }
        
        $filename = uniqid() . '.' . $file_extension;
        $filepath = $upload_dir . $filename;
        
        if(!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        if(move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
        }
        
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }
    
    // Format currency
    public static function formatCurrency($amount, $currency = '$') {
        return $currency . number_format($amount, 2);
    }
    
    // Time ago function
    public static function timeAgo($datetime) {
        $time = time() - strtotime($datetime);
        
        if($time < 60) return 'just now';
        if($time < 3600) return floor($time/60) . ' minutes ago';
        if($time < 86400) return floor($time/3600) . ' hours ago';
        if($time < 2592000) return floor($time/86400) . ' days ago';
        if($time < 31536000) return floor($time/2592000) . ' months ago';
        
        return floor($time/31536000) . ' years ago';
    }
}
?>