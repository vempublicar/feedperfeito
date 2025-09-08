<?php
// Supabase configuration
define('SUPABASE_URL', 'https://phbnwlplkdawjgfyscxe.supabase.co');
define('SUPABASE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InBoYm53bHBsa2Rhd2pnZnlzY3hlIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTcyOTc3NTgsImV4cCI6MjA3Mjg3Mzc1OH0.DrKcSbnC7iENx_Xv94OhJFZ_xTLBxYrlNk7YePESdhk');

// Function to make API requests to Supabase
function supabase_request($endpoint, $method = 'GET', $data = null) {
    $url = SUPABASE_URL . '/rest/v1/' . $endpoint;
    
    $headers = [
        'apikey: ' . SUPABASE_KEY,
        'Authorization: Bearer ' . SUPABASE_KEY,
        'Content-Type: application/json',
        'Prefer: return=representation'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // SSL certificate options
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desabilitado para testes locais
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);     // Desabilitado para testes locais
    // curl_setopt($ch, CURLOPT_CAINFO, 'C:\xampp\apache\bin\curl-ca-bundle.crt');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_error($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("cURL Error: " . $error);
    }
    
    curl_close($ch);
    
    if ($httpCode >= 400) {
        throw new Exception("HTTP Error: " . $httpCode . " - Response: " . $response);
    }
    
    return json_decode($response, true);
}

// Function to get all records from a table
function get_all($table) {
    try {
        return supabase_request($table);
    } catch (Exception $e) {
        error_log("Error getting records from " . $table . ": " . $e->getMessage());
        return [];
    }
}

// Function to get a record by ID
function get_by_id($table, $id) {
    try {
        $result = supabase_request($table . '?id=eq.' . $id);
        return $result ? $result[0] : null;
    } catch (Exception $e) {
        error_log("Error getting record from " . $table . " with id " . $id . ": " . $e->getMessage());
        return null;
    }
}

// Function to insert a new record
function insert($table, $data) {
    try {
        return supabase_request($table, 'POST', $data);
    } catch (Exception $e) {
        error_log("Error inserting record into " . $table . ": " . $e->getMessage());
        return false;
    }
}

// Function to update a record
function update($table, $id, $data) {
    try {
        return supabase_request($table . '?id=eq.' . $id, 'PATCH', $data);
    } catch (Exception $e) {
        error_log("Error updating record in " . $table . " with id " . $id . ": " . $e->getMessage());
        return false;
    }
}

// Function to delete a record
function delete($table, $id) {
    try {
        return supabase_request($table . '?id=eq.' . $id, 'DELETE');
    } catch (Exception $e) {
        error_log("Error deleting record from " . $table . " with id " . $id . ": " . $e->getMessage());
        return false;
    }
}

// Function to execute SQL queries via Supabase SQL API
function supabase_execute_sql($sql) {
    $url = SUPABASE_URL . '/rest/v1/rpc/execute_sql'; // This might not be the correct endpoint
    
    $headers = [
        'apikey: ' . SUPABASE_KEY,
        'Authorization: Bearer ' . SUPABASE_KEY,
        'Content-Type: application/json',
        'Prefer: return=representation'
    ];
    
    $data = [
        'sql' => $sql
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // SSL certificate options
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desabilitado para testes locais
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);     // Desabilitado para testes locais
    // curl_setopt($ch, CURLOPT_CAINFO, 'C:\xampp\apache\bin\curl-ca-bundle.crt');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_error($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("cURL Error: " . $error);
    }
    
    curl_close($ch);
    
    if ($httpCode >= 400) {
        throw new Exception("HTTP Error: " . $httpCode . " - Response: " . $response);
    }
    
    return json_decode($response, true);
}

?>