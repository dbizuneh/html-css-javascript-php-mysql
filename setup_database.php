<?php
/**
 * Database Setup Script for Medical Records System
 * Run this script once to set up the database with sample data
 */

// Database configuration
$host = 'localhost';
$username = 'root';  // Change this to your MySQL username
$password = '';      // Change this to your MySQL password
$database = 'medical_records_db';

echo "<h2>Medical Records System - Database Setup</h2>";
echo "<p>Setting up database with sample data...</p>";

try {
    // Connect to MySQL (without selecting database first)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✓ Connected to MySQL server</p>";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $database");
    echo "<p>✓ Database '$database' created/verified</p>";
    
    // Select the database
    $pdo->exec("USE $database");
    
    // Read and execute schema.sql
    if (file_exists('database/schema.sql')) {
        $schema = file_get_contents('database/schema.sql');
        $statements = explode(';', $schema);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        echo "<p>✓ Database schema created successfully</p>";
    } else {
        throw new Exception("Schema file not found: database/schema.sql");
    }
    
    // Read and execute sample_data.sql
    if (file_exists('database/sample_data.sql')) {
        $sampleData = file_get_contents('database/sample_data.sql');
        $statements = explode(';', $sampleData);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement) && !preg_match('/^SELECT/', $statement)) {
                $pdo->exec($statement);
            }
        }
        echo "<p>✓ Sample data inserted successfully</p>";
    } else {
        throw new Exception("Sample data file not found: database/sample_data.sql");
    }
    
    // Update database configuration file
    $configContent = "<?php
class Database {
    private \$host = '$host';
    private \$db_name = '$database';
    private \$username = '$username';
    private \$password = '$password';
    private \$conn;

    public function getConnection() {
        \$this->conn = null;
        
        try {
            \$this->conn = new PDO(
                \"mysql:host=\" . \$this->host . \";dbname=\" . \$this->db_name,
                \$this->username,
                \$this->password
            );
            \$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException \$exception) {
            echo \"Connection error: \" . \$exception->getMessage();
        }
        
        return \$this->conn;
    }
}
?>";
    
    file_put_contents('config/database.php', $configContent);
    echo "<p>✓ Database configuration updated</p>";
    
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #155724; margin: 0 0 10px 0;'>✅ Setup Complete!</h3>";
    echo "<p style='margin: 5px 0;'><strong>Database:</strong> $database</p>";
    echo "<p style='margin: 5px 0;'><strong>Tables Created:</strong> All medical records tables</p>";
    echo "<p style='margin: 5px 0;'><strong>Sample Data:</strong> Patients, doctors, and medical records added</p>";
    echo "</div>";
    
    echo "<div style='background: #cce5ff; padding: 15px; border: 1px solid #80bdff; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #004085; margin: 0 0 10px 0;'>🔑 Sample Login Credentials</h3>";
    echo "<p style='margin: 5px 0;'><strong>Password for all accounts:</strong> password123</p>";
    echo "<h4 style='color: #004085; margin: 15px 0 5px 0;'>Patients:</h4>";
    echo "<ul style='margin: 5px 0;'>";
    echo "<li>john.doe (John Doe)</li>";
    echo "<li>jane.smith (Jane Smith)</li>";
    echo "<li>mike.johnson (Mike Johnson)</li>";
    echo "</ul>";
    echo "<h4 style='color: #004085; margin: 15px 0 5px 0;'>Doctors:</h4>";
    echo "<ul style='margin: 5px 0;'>";
    echo "<li>dr.wilson (Dr. Sarah Wilson)</li>";
    echo "<li>dr.brown (Dr. David Brown)</li>";
    echo "</ul>";
    echo "<h4 style='color: #004085; margin: 15px 0 5px 0;'>Surgeons:</h4>";
    echo "<ul style='margin: 5px 0;'>";
    echo "<li>dr.garcia (Dr. Maria Garcia)</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #856404; margin: 0 0 10px 0;'>🚀 Next Steps</h3>";
    echo "<ol style='margin: 5px 0;'>";
    echo "<li>Visit <a href='index.php'>index.php</a> to start using the system</li>";
    echo "<li>Login with any of the sample credentials above</li>";
    echo "<li>Test patient and medical staff features</li>";
    echo "<li>For production use, change database credentials and passwords</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #721c24; margin: 0 0 10px 0;'>❌ Setup Failed</h3>";
    echo "<p style='color: #721c24;'>Error: " . $e->getMessage() . "</p>";
    echo "<p style='color: #721c24;'>Please check your database credentials and try again.</p>";
    echo "</div>";
    
    echo "<div style='background: #cce5ff; padding: 15px; border: 1px solid #80bdff; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #004085; margin: 0 0 10px 0;'>💡 Troubleshooting</h3>";
    echo "<ol style='margin: 5px 0;'>";
    echo "<li>Make sure MySQL is running</li>";
    echo "<li>Check username and password in this file (lines 7-8)</li>";
    echo "<li>Ensure MySQL user has database creation privileges</li>";
    echo "<li>Verify that database files exist in the 'database/' folder</li>";
    echo "</ol>";
    echo "</div>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f8f9fa;
}
h2 {
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}
p {
    color: #666;
    line-height: 1.6;
}
a {
    color: #007bff;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>