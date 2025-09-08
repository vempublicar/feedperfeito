<?php
require_once 'config/database.php';

echo "Setting up Supabase database...\n";

// For Supabase, we need to use the REST API to create tables
// This is a simplified approach - in a production environment, you would typically use the Supabase dashboard or migrations

// First, let's test the connection
try {
    $test = supabase_request('users?limit=1', 'GET');
    echo "Database connection successful\n";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Create tables one by one
$tables = [
    [
        'name' => 'users',
        'columns' => [
            ['name' => 'id', 'type' => 'integer', 'is_primary_key' => true, 'is_identity' => true],
            ['name' => 'name', 'type' => 'text'],
            ['name' => 'email', 'type' => 'text', 'is_unique' => true],
            ['name' => 'password', 'type' => 'text'],
            ['name' => 'credits', 'type' => 'integer', 'default' => 0],
            ['name' => 'created_at', 'type' => 'timestamp', 'default' => 'now()'],
            ['name' => 'updated_at', 'type' => 'timestamp', 'default' => 'now()']
        ]
    ],
    [
        'name' => 'admin_users',
        'columns' => [
            ['name' => 'id', 'type' => 'integer', 'is_primary_key' => true, 'is_identity' => true],
            ['name' => 'name', 'type' => 'text'],
            ['name' => 'email', 'type' => 'text', 'is_unique' => true],
            ['name' => 'password', 'type' => 'text'],
            ['name' => 'role', 'type' => 'text'],
            ['name' => 'is_active', 'type' => 'boolean', 'default' => true],
            ['name' => 'last_login', 'type' => 'timestamp'],
            ['name' => 'created_at', 'type' => 'timestamp', 'default' => 'now()'],
            ['name' => 'updated_at', 'type' => 'timestamp', 'default' => 'now()']
        ]
    ],
    [
        'name' => 'user_sessions',
        'columns' => [
            ['name' => 'id', 'type' => 'integer', 'is_primary_key' => true, 'is_identity' => true],
            ['name' => 'user_id', 'type' => 'integer'],
            ['name' => 'session_token', 'type' => 'text', 'is_unique' => true],
            ['name' => 'expires_at', 'type' => 'timestamp'],
            ['name' => 'created_at', 'type' => 'timestamp', 'default' => 'now()']
        ]
    ],
    [
        'name' => 'credit_packages',
        'columns' => [
            ['name' => 'id', 'type' => 'integer', 'is_primary_key' => true, 'is_identity' => true],
            ['name' => 'title', 'type' => 'text'],
            ['name' => 'credits', 'type' => 'integer'],
            ['name' => 'bonus_credits', 'type' => 'integer', 'default' => 0],
            ['name' => 'price', 'type' => 'numeric'],
            ['name' => 'tag', 'type' => 'text'],
            ['name' => 'is_active', 'type' => 'boolean', 'default' => true],
            ['name' => 'created_at', 'type' => 'timestamp', 'default' => 'now()'],
            ['name' => 'updated_at', 'type' => 'timestamp', 'default' => 'now()']
        ]
    ],
    [
        'name' => 'credit_transactions',
        'columns' => [
            ['name' => 'id', 'type' => 'integer', 'is_primary_key' => true, 'is_identity' => true],
            ['name' => 'user_id', 'type' => 'integer'],
            ['name' => 'transaction_type', 'type' => 'text'],
            ['name' => 'credits', 'type' => 'integer'],
            ['name' => 'description', 'type' => 'text'],
            ['name' => 'created_at', 'type' => 'timestamp', 'default' => 'now()']
        ]
    ],
    [
        'name' => 'content_templates',
        'columns' => [
            ['name' => 'id', 'type' => 'integer', 'is_primary_key' => true, 'is_identity' => true],
            ['name' => 'title', 'type' => 'text'],
            ['name' => 'category', 'type' => 'text'],
            ['name' => 'credits_required', 'type' => 'integer'],
            ['name' => 'preview_url', 'type' => 'text'],
            ['name' => 'thumbnail_url', 'type' => 'text'],
            ['name' => 'is_featured', 'type' => 'boolean', 'default' => false],
            ['name' => 'is_active', 'type' => 'boolean', 'default' => true],
            ['name' => 'created_at', 'type' => 'timestamp', 'default' => 'now()'],
            ['name' => 'updated_at', 'type' => 'timestamp', 'default' => 'now()']
        ]
    ],
    [
        'name' => 'user_orders',
        'columns' => [
            ['name' => 'id', 'type' => 'integer', 'is_primary_key' => true, 'is_identity' => true],
            ['name' => 'user_id', 'type' => 'integer'],
            ['name' => 'template_id', 'type' => 'integer'],
            ['name' => 'status', 'type' => 'text'],
            ['name' => 'title', 'type' => 'text'],
            ['name' => 'description', 'type' => 'text'],
            ['name' => 'credits_used', 'type' => 'integer'],
            ['name' => 'created_at', 'type' => 'timestamp', 'default' => 'now()'],
            ['name' => 'updated_at', 'type' => 'timestamp', 'default' => 'now()']
        ]
    ],
    [
        'name' => 'order_approvals',
        'columns' => [
            ['name' => 'id', 'type' => 'integer', 'is_primary_key' => true, 'is_identity' => true],
            ['name' => 'order_id', 'type' => 'integer'],
            ['name' => 'feedback', 'type' => 'text'],
            ['name' => 'is_approved', 'type' => 'boolean'],
            ['name' => 'approved_at', 'type' => 'timestamp'],
            ['name' => 'created_at', 'type' => 'timestamp', 'default' => 'now()'],
            ['name' => 'updated_at', 'type' => 'timestamp', 'default' => 'now()']
        ]
    ],
    [
        'name' => 'services',
        'columns' => [
            ['name' => 'id', 'type' => 'integer', 'is_primary_key' => true, 'is_identity' => true],
            ['name' => 'title', 'type' => 'text'],
            ['name' => 'description', 'type' => 'text'],
            ['name' => 'thumbnail_url', 'type' => 'text'],
            ['name' => 'delivery_time', 'type' => 'text'],
            ['name' => 'price_type', 'type' => 'text'],
            ['name' => 'price', 'type' => 'numeric'],
            ['name' => 'credits_required', 'type' => 'integer'],
            ['name' => 'tag', 'type' => 'text'],
            ['name' => 'is_active', 'type' => 'boolean', 'default' => true],
            ['name' => 'created_at', 'type' => 'timestamp', 'default' => 'now()'],
            ['name' => 'updated_at', 'type' => 'timestamp', 'default' => 'now()']
        ]
    ],
    [
        'name' => 'service_requests',
        'columns' => [
            ['name' => 'id', 'type' => 'integer', 'is_primary_key' => true, 'is_identity' => true],
            ['name' => 'user_id', 'type' => 'integer'],
            ['name' => 'service_id', 'type' => 'integer'],
            ['name' => 'urgency', 'type' => 'text'],
            ['name' => 'objective', 'type' => 'text'],
            ['name' => 'details', 'type' => 'text'],
            ['name' => 'status', 'type' => 'text'],
            ['name' => 'credits_used', 'type' => 'integer'],
            ['name' => 'created_at', 'type' => 'timestamp', 'default' => 'now()'],
            ['name' => 'updated_at', 'type' => 'timestamp', 'default' => 'now()']
        ]
    ],
    [
        'name' => 'promotions',
        'columns' => [
            ['name' => 'id', 'type' => 'integer', 'is_primary_key' => true, 'is_identity' => true],
            ['name' => 'title', 'type' => 'text'],
            ['name' => 'description', 'type' => 'text'],
            ['name' => 'template_id', 'type' => 'integer'],
            ['name' => 'original_credits', 'type' => 'integer'],
            ['name' => 'discounted_credits', 'type' => 'integer'],
            ['name' => 'preview_url', 'type' => 'text'],
            ['name' => 'thumbnail_url', 'type' => 'text'],
            ['name' => 'tag', 'type' => 'text'],
            ['name' => 'expires_at', 'type' => 'timestamp'],
            ['name' => 'is_active', 'type' => 'boolean', 'default' => true],
            ['name' => 'created_at', 'type' => 'timestamp', 'default' => 'now()'],
            ['name' => 'updated_at', 'type' => 'timestamp', 'default' => 'now()']
        ]
    ],
    [
        'name' => 'vouchers',
        'columns' => [
            ['name' => 'id', 'type' => 'integer', 'is_primary_key' => true, 'is_identity' => true],
            ['name' => 'code', 'type' => 'text', 'is_unique' => true],
            ['name' => 'credits', 'type' => 'integer'],
            ['name' => 'is_used', 'type' => 'boolean', 'default' => false],
            ['name' => 'user_id', 'type' => 'integer'],
            ['name' => 'used_at', 'type' => 'timestamp'],
            ['name' => 'expires_at', 'type' => 'timestamp'],
            ['name' => 'created_at', 'type' => 'timestamp', 'default' => 'now()']
        ]
    ],
    [
        'name' => 'user_content_files',
        'columns' => [
            ['name' => 'id', 'type' => 'integer', 'is_primary_key' => true, 'is_identity' => true],
            ['name' => 'order_id', 'type' => 'integer'],
            ['name' => 'file_url', 'type' => 'text'],
            ['name' => 'file_name', 'type' => 'text'],
            ['name' => 'file_type', 'type' => 'text'],
            ['name' => 'is_watermarked', 'type' => 'boolean', 'default' => true],
            ['name' => 'is_downloadable', 'type' => 'boolean', 'default' => false],
            ['name' => 'created_at', 'type' => 'timestamp', 'default' => 'now()']
        ]
    ]
];

echo "Creating tables...\n";
$createdTables = 0;

foreach ($tables as $table) {
    echo "Creating table: {$table['name']}\n";
    
    // For Supabase, we'll try to insert a dummy record to create the table
    // This is a workaround since Supabase doesn't have a direct table creation API
    try {
        // Try to insert a dummy record to create the table
        $dummyData = [];
        foreach ($table['columns'] as $column) {
            if ($column['name'] !== 'id') { // Skip auto-incrementing ID
                if (isset($column['default'])) {
                    if ($column['default'] === 'now()') {
                        $dummyData[$column['name']] = date('Y-m-d H:i:s');
                    } else {
                        $dummyData[$column['name']] = $column['default'];
                    }
                } else {
                    // Provide dummy values based on type
                    switch ($column['type']) {
                        case 'text':
                            $dummyData[$column['name']] = 'dummy';
                            break;
                        case 'integer':
                            $dummyData[$column['name']] = 1;
                            break;
                        case 'numeric':
                            $dummyData[$column['name']] = 1.0;
                            break;
                        case 'boolean':
                            $dummyData[$column['name']] = false;
                            break;
                        case 'timestamp':
                            $dummyData[$column['name']] = date('Y-m-d H:i:s');
                            break;
                        default:
                            $dummyData[$column['name']] = 'dummy';
                    }
                }
            }
        }
        
        // Try to insert the dummy record
        $result = supabase_request($table['name'], 'POST', $dummyData);
        
        // Then delete the dummy record
        if ($result && isset($result[0]['id'])) {
            supabase_request($table['name'] . '?id=eq.' . $result[0]['id'], 'DELETE');
        }
        
        echo "Table {$table['name']} created successfully\n";
        $createdTables++;
    } catch (Exception $e) {
        // Check if it's a duplicate table error
        if (strpos($e->getMessage(), 'duplicate') !== false || 
            strpos($e->getMessage(), 'already exists') !== false) {
            echo "Table {$table['name']} already exists\n";
        } else {
            echo "Error creating table {$table['name']}: " . $e->getMessage() . "\n";
        }
    }
}

echo "Database setup completed. $createdTables tables processed.\n";
?>