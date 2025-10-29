<?php
/**
 * Wishlist Table Installation Script
 * Run this file once to create the wishlist table in your database
 */

require_once 'config/db.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Install Wishlist Table</title>
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
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #00e600;
            padding-bottom: 10px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #17a2b8;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #00e600;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }
        .btn:hover {
            background: #00cc00;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🛠️ Wishlist Table Installation</h1>";

// Create wishlist table
$sql = "CREATE TABLE IF NOT EXISTS wishlist (
    wishlist_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT NULL,
    FOREIGN KEY (user_id) REFERENCES user_credentials(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id),
    INDEX idx_product (product_id),
    INDEX idx_added_at (added_at)
)";

if ($conn->query($sql) === TRUE) {
    echo "<div class='success'>✅ Wishlist table created successfully!</div>";
    
    // Check if we should add sample data
    $check_sql = "SELECT COUNT(*) as count FROM wishlist";
    $result = $conn->query($check_sql);
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        echo "<div class='info'>📝 Adding sample wishlist data...</div>";
        
        // Add sample data (adjust user_id and product_id based on your data)
        $sample_sql = "INSERT INTO wishlist (user_id, product_id, notes) VALUES
                      (2, 9, 'Need this for the new clinic')";
        
        if ($conn->query($sample_sql) === TRUE) {
            echo "<div class='success'>✅ Sample data added successfully!</div>";
        } else {
            echo "<div class='info'>ℹ️ Sample data not added (this is okay if you don't have matching user/product IDs)</div>";
        }
    } else {
        echo "<div class='info'>ℹ️ Wishlist table already contains data. Skipping sample data insertion.</div>";
    }
    
    echo "<div class='success'>
            <h3>Installation Complete! 🎉</h3>
            <p>The wishlist feature is now ready to use.</p>
            <ul>
                <li>✅ Wishlist table created</li>
                <li>✅ Foreign keys configured</li>
                <li>✅ Indexes added for performance</li>
            </ul>
          </div>";
    
    echo "<a href='client/index.php?page=wishlist' class='btn'>Go to Wishlist Page</a>";
    echo " <a href='client/index.php?page=dashboard' class='btn' style='background: #667eea;'>Go to Dashboard</a>";
    
} else {
    echo "<div class='error'>❌ Error creating table: " . $conn->error . "</div>";
    
    // Check if table already exists
    $check_table = "SHOW TABLES LIKE 'wishlist'";
    $result = $conn->query($check_table);
    
    if ($result->num_rows > 0) {
        echo "<div class='info'>ℹ️ The wishlist table already exists in your database.</div>";
        echo "<a href='client/index.php?page=wishlist' class='btn'>Go to Wishlist Page</a>";
    }
}

echo "    </div>
</body>
</html>";

$conn->close();
?>

