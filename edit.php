<?php

require_once "db.php"; // Include the database class

// Set headers for CORS (Cross-Origin Resource Sharing) and content type
header('Content-Type: application/json'); // Ensure response is in JSON format
header('Access-Control-Allow-Origin: *'); // Allow all domains (or specify domain if needed)
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // Allow POST, GET, OPTIONS methods
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Allow these headers

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate user inputs
    $id = trim($_POST['id']);
    $contactName = trim($_POST['contactName']);
    $contactPhoneNumber = trim($_POST['contactPhoneNumber']);
    $broadcastSms = trim($_POST['broadcastSms']);


    $requiredFields = ['id', 'contactName', 'contactPhoneNumber'];

    // Check if all required fields are present and not empty
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            // If any required field is missing or empty, return an error message
            http_response_code(400);
            echo json_encode(['message' => "$field is missing or empty", 'status' => 0]);
            exit(); // Exit script
        }
    }
    $contactPhoneNumber = '+91 ' . $contactPhoneNumber;

    // Prepare data for updating
    $set = [
        'contactName' => $contactName,
        'contactPhoneNumber' => $contactPhoneNumber,
        'broadcastSms' => $broadcastSms

    ];

    // Prepare where condition
    $where = [
        'id' => $id
    ];

    // Update data in the database
    if (DB::update('contact', $set, $where)) {
        // Return a JSON response for success
        echo json_encode([
            'status' => 'success',
            'message' => 'Contact updated successfully!'
        ]);
    } else {
        // Log database errors
        error_log("Database error: " . DB::update('contact', $set, $where, true));
        // Return a JSON response for database error
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update contact in the database.'
        ]);
    }
} else {
    // Handle invalid requests
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Bad request'
    ]);
}

?>
