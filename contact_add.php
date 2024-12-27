<?php

require_once "db.php"; // Include the database class

// Set headers for CORS (Cross-Origin Resource Sharing) and content type
header('Content-Type: application/json'); // Ensure response is in JSON format
header('Access-Control-Allow-Origin: *'); // Allow all domains (or specify domain if needed)
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // Allow POST, GET, OPTIONS methods
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Allow these headers

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate user inputs
    $contactName = trim($_POST['contactName']);
    $contactPhoneNumber = trim($_POST['contactPhoneNumber']);
    $broadcastSms = trim($_POST['broadcastSms']);
    $dateSubmitted = trim($_POST['dateSubmitted']);
    $daySubmitted = trim($_POST['daySubmitted']);
    $currentTime = trim($_POST['currentTime']);



   
    $requiredFields = ['contactName', 'contactPhoneNumber'];

    // Check if all required fields are present and not empty
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            // If any required field is missing or empty, return an error message
            http_response_code(400);
            echo json_encode(['message' => "$field is missing or empty", 'status' => 0]);
            exit(); // Exit script
        }
    }

    // Prepend +91 to the phone number
    $contactPhoneNumber = '+91 ' . $contactPhoneNumber;

    // Prepare data for insertion
    $data = [
        'contactName' => $contactName,
        'contactPhoneNumber' => $contactPhoneNumber,
        'broadcastSms' => $broadcastSms,
        'dateSubmitted' => $dateSubmitted,
        'daySubmitted' => $daySubmitted,
        'currentTime' => $currentTime



    ];

    // Insert data into the database
    if (DB::insert('contact', $data)) {
        // Return a JSON response for success
        echo json_encode([
            'status' => 'success',
            'message' => 'Contact added successfully!',
        ]);
    } else {
        // Log database errors
        error_log("Database error: ");
        // Return a JSON response for database error
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to insert contact into the database.' . DB::insert('contact', $data, true)
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
