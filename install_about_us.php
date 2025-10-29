<?php
/**
 * Installation Script for About Us Content Table
 * Run this file once to create the about_us_content table and insert default content
 */

// Database configuration
require_once 'config/db.php';

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Install About Us Content</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #00e600;
            border-bottom: 3px solid #00e600;
            padding-bottom: 10px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid #17a2b8;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #00e600;
            color: black;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
            font-weight: bold;
        }
        .btn:hover {
            background: #00b300;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>📄 About Us Content Installation</h1>";

try {
    // Read SQL file
    $sql_file = 'used/about_us.sql';
    
    if (!file_exists($sql_file)) {
        throw new Exception("SQL file not found: $sql_file");
    }
    
    $sql = file_get_contents($sql_file);
    
    if ($sql === false) {
        throw new Exception("Failed to read SQL file");
    }
    
    echo "<div class='info'>📖 SQL file loaded successfully</div>";
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && 
                   !preg_match('/^--/', $stmt) && 
                   !preg_match('/^\s*$/', $stmt);
        }
    );
    
    echo "<div class='info'>📝 Found " . count($statements) . " SQL statements to execute</div>";
    
    // Execute each statement
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement)) continue;
        
        try {
            if ($conn->query($statement) === TRUE) {
                $success_count++;
                
                // Show what was executed
                if (stripos($statement, 'CREATE TABLE') !== false) {
                    echo "<div class='success'>✅ Table created successfully</div>";
                } elseif (stripos($statement, 'INSERT INTO') !== false) {
                    echo "<div class='success'>✅ Content inserted successfully</div>";
                } elseif (stripos($statement, 'CREATE INDEX') !== false) {
                    echo "<div class='success'>✅ Index created successfully</div>";
                }
            } else {
                throw new Exception($conn->error);
            }
        } catch (Exception $e) {
            $error_count++;
            // Check if error is about table already existing
            if (stripos($e->getMessage(), 'already exists') !== false) {
                echo "<div class='info'>ℹ️ Table already exists - skipping creation</div>";
            } elseif (stripos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "<div class='info'>ℹ️ Content already exists - skipping insertion</div>";
            } else {
                echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
    
    echo "<div class='success'>
            <h3>✅ Installation Complete!</h3>
            <p><strong>Successful operations:</strong> $success_count</p>
            <p><strong>Errors/Skipped:</strong> $error_count</p>
          </div>";
    
    // Verify installation
    $result = $conn->query("SELECT COUNT(*) as count FROM about_us_content");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<div class='success'>
                <h3>📊 Verification</h3>
                <p>Total content sections in database: <strong>" . $row['count'] . "</strong></p>
              </div>";
    }
    
    // Show sample data
    echo "<div class='info'>
            <h3>📋 Sample Content Sections</h3>
            <ul>";
    
    $result = $conn->query("SELECT section_name, section_title FROM about_us_content ORDER BY section_order LIMIT 5");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<li><strong>" . htmlspecialchars($row['section_name']) . ":</strong> " . 
                 htmlspecialchars($row['section_title']) . "</li>";
        }
    }
    
    echo "</ul></div>";
    
    echo "<div class='info'>
            <h3>🎯 Next Steps</h3>
            <ol>
                <li>Visit the About Us page to see the content</li>
                <li>Use the admin panel to edit content (if available)</li>
                <li>Add images to the <code>images/about/</code> folder</li>
                <li>Delete this installation file for security</li>
            </ol>
          </div>";
    
    echo "<a href='index.php?page=about' class='btn'>📄 View About Us Page</a>";
    echo " <a href='index.php' class='btn' style='background: #007bff;'>🏠 Go to Home</a>";
    
} catch (Exception $e) {
    echo "<div class='error'>
            <h3>❌ Installation Failed</h3>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
          </div>";
    
    echo "<a href='install_about_us.php' class='btn' style='background: #dc3545;'>🔄 Try Again</a>";
}

$conn->close();

echo "
    </div>
</body>
</html>";
?>

