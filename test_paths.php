<?php
echo "<h2>File Path Test</h2>";

echo "<h3>Current Directory Structure:</h3>";
echo "<p><strong>Current working directory:</strong> " . getcwd() . "</p>";
echo "<p><strong>Script filename:</strong> " . __FILE__ . "</p>";
echo "<p><strong>Script directory:</strong> " . __DIR__ . "</p>";

echo "<h3>File Existence Tests:</h3>";

$files_to_check = [
    'config/database.php',
    'includes/auth.php',
    'classes/MedicalRecord.php',
    'database/schema.sql',
    'database/sample_data.sql'
];

foreach ($files_to_check as $file) {
    $exists = file_exists($file);
    $icon = $exists ? '✅' : '❌';
    echo "<p>$icon <strong>$file:</strong> " . ($exists ? 'EXISTS' : 'NOT FOUND') . "</p>";
    
    if ($exists) {
        echo "<p style='margin-left: 20px; color: #666;'>Full path: " . realpath($file) . "</p>";
    }
}

echo "<h3>Directory Contents:</h3>";

$directories = ['.', 'config', 'includes', 'classes', 'database'];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        echo "<h4>Directory: $dir/</h4>";
        $files = scandir($dir);
        echo "<ul>";
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "<li>$file</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p>❌ Directory '$dir' not found</p>";
    }
}

echo "<h3>Include Path Test:</h3>";
try {
    if (file_exists('config/database.php')) {
        require_once 'config/database.php';
        echo "<p>✅ config/database.php included successfully</p>";
        
        // Test database connection
        try {
            $db = new Database();
            $conn = $db->getConnection();
            if ($conn) {
                echo "<p>✅ Database connection successful</p>";
            } else {
                echo "<p>❌ Database connection failed</p>";
            }
        } catch (Exception $e) {
            echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>❌ config/database.php not found</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Include error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
h2, h3, h4 { color: #333; }
p { margin: 5px 0; }
ul { margin: 10px 0; }
</style>