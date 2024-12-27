<?php

require_once "db.php"; // Include the database class

// Set headers for CORS and content type
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header('Content-Type: application/json'); // Ensure response is in JSON format

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Fetch all contact data using the select method
    DB::select('*', "contact",);

    if (DB::select('*', "contact")) {
        // Return the contact data as a JSON response
        echo json_encode([
            'message' => 'Contacts fetched successfully',
            'data' => DB::select('*', "contact")
        ]);
    } else {
        echo json_encode([
            'message' => 'No contacts found'
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Bad request'
    ]);
}

?>
