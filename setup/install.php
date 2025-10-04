<?php
require_once '../config/database.php';

echo "<h2>Setting up WorkForage Database...</h2>";

try {
    // Create database connection
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute the schema file
    $schema = file_get_contents('../database/schema.sql');
    
    // Split the schema into individual statements
    $statements = explode(';', $schema);
    
    foreach($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "<div style='color: green;'>✅ Database setup completed successfully!</div>";
    echo "<div style='margin-top: 20px;'>";
    echo "<h3>Test Accounts Created:</h3>";
    echo "<strong>Freelancer Account:</strong><br>";
    echo "Email: john@example.com<br>";
    echo "Password: password123<br><br>";
    echo "<strong>Business Account:</strong><br>";
    echo "Email: jane@example.com<br>";
    echo "Password: password123<br><br>";
    echo "<a href='../html/login_new.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<div style='color: red;'>❌ Error setting up database: " . $e->getMessage() . "</div>";
}
?>