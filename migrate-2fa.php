<?php
// migrate-2fa.php - Add 2FA columns to users table
// Run this script once to add the necessary columns for 2FA support

require_once 'config/database.php';

$database = new Database();

try {
    echo "<style>
        body { font-family: Arial, sans-serif; background: #1a1a1a; color: #fff; margin: 20px; }
        .success { color: #20bf6b; padding: 10px; background: #0d3d1a; border-radius: 5px; margin: 10px 0; }
        .error { color: #ff6b6b; padding: 10px; background: #3d0d0d; border-radius: 5px; margin: 10px 0; }
        .info { color: #4dabf7; padding: 10px; background: #0d1f3d; border-radius: 5px; margin: 10px 0; }
        h1 { color: #ffd700; }
    </style>";
    
    echo "<h1>🔐 2FA Migration Script</h1>";
    echo "<p>This script will add 2FA columns to your users table...</p>";
    
    // Check if columns already exist
    echo "<div class='info'>Checking if columns already exist...</div>";
    
    $query = "SHOW COLUMNS FROM users";
    $stmt = $database->executeQuery($query);
    $columns = $database->fetchAll($stmt);
    
    $columnNames = array_column($columns, 'Field');
    
    $needsMigration = false;
    $missingColumns = [];
    
    if (!in_array('two_fa_enabled', $columnNames)) {
        $missingColumns[] = 'two_fa_enabled';
        $needsMigration = true;
    }
    if (!in_array('two_fa_secret', $columnNames)) {
        $missingColumns[] = 'two_fa_secret';
        $needsMigration = true;
    }
    if (!in_array('two_fa_backup_codes', $columnNames)) {
        $missingColumns[] = 'two_fa_backup_codes';
        $needsMigration = true;
    }
    
    if (!$needsMigration) {
        echo "<div class='success'>✅ All 2FA columns already exist! Migration complete.</div>";
    } else {
        echo "<div class='info'>Found missing columns: " . implode(', ', $missingColumns) . "</div>";
        
        // Add missing columns
        if (in_array('two_fa_enabled', $missingColumns)) {
            echo "<p>Adding column: two_fa_enabled...</p>";
            $query = "ALTER TABLE users ADD COLUMN two_fa_enabled BOOLEAN DEFAULT 0";
            $database->executeQuery($query);
            echo "<div class='success'>✅ Added column: two_fa_enabled</div>";
        }
        
        if (in_array('two_fa_secret', $missingColumns)) {
            echo "<p>Adding column: two_fa_secret...</p>";
            $query = "ALTER TABLE users ADD COLUMN two_fa_secret VARCHAR(32) NULL";
            $database->executeQuery($query);
            echo "<div class='success'>✅ Added column: two_fa_secret</div>";
        }
        
        if (in_array('two_fa_backup_codes', $missingColumns)) {
            echo "<p>Adding column: two_fa_backup_codes...</p>";
            $query = "ALTER TABLE users ADD COLUMN two_fa_backup_codes JSON NULL";
            $database->executeQuery($query);
            echo "<div class='success'>✅ Added column: two_fa_backup_codes</div>";
        }
        
        echo "<div class='success'>🎉 Migration completed successfully!</div>";
    }
    
    echo "<p style='margin-top: 30px; border-top: 1px solid #555; padding-top: 20px;'>";
    echo "You can now <a href='login.php' style='color: #ffd700; text-decoration: none;'><strong>go back to login</strong></a>";
    echo "</p>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Migration failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<p>Please ensure your database connection is working correctly.</p>";
}
?>
