<?php
require_once 'config/database.php';

class BaseModel {
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct() {
        // Constructor can be extended in child classes
    }
    
    // Get all records
    public function all() {
        try {
            return get_all($this->table);
        } catch (Exception $e) {
            error_log("Error in all() for table " . $this->table . ": " . $e->getMessage());
            return [];
        }
    }
    
    // Find a record by ID
    public function find($id) {
        try {
            return get_by_id($this->table, $id);
        } catch (Exception $e) {
            error_log("Error in find() for table " . $this->table . " with id " . $id . ": " . $e->getMessage());
            return null;
        }
    }
    
    // Create a new record
    public function create($data) {
        try {
            // Add timestamps if they exist in the table
            if (!isset($data['created_at'])) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }
            if (!isset($data['updated_at'])) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }
            
            return insert($this->table, $data);
        } catch (Exception $e) {
            error_log("Error in create() for table " . $this->table . ": " . $e->getMessage());
            return false;
        }
    }
    
    // Update a record
    public function update($id, $data) {
        try {
            // Add updated_at timestamp
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            return update($this->table, $id, $data);
        } catch (Exception $e) {
            error_log("Error in update() for table " . $this->table . " with id " . $id . ": " . $e->getMessage());
            return false;
        }
    }
    
    // Delete a record
    public function delete($id) {
        try {
            return delete($this->table, $id);
        } catch (Exception $e) {
            error_log("Error in delete() for table " . $this->table . " with id " . $id . ": " . $e->getMessage());
            return false;
        }
    }
    
    // Find records with custom conditions
    public function where($conditions) {
        try {
            $query = $this->table . '?';
            $conditionStrings = [];
            
            foreach ($conditions as $key => $value) {
                $conditionStrings[] = $key . '=eq.' . $value;
            }
            
            $query .= implode('&', $conditionStrings);
            return supabase_request($query);
        } catch (Exception $e) {
            error_log("Error in where() for table " . $this->table . ": " . $e->getMessage());
            return [];
        }
    }
    
    // Find records with custom query
    public function query($query) {
        try {
            return supabase_request($this->table . $query);
        } catch (Exception $e) {
            error_log("Error in query() for table " . $this->table . " with query " . $query . ": " . $e->getMessage());
            return [];
        }
    }
}
?>