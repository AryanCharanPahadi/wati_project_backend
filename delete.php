<?php

require_once "db.php"; // Include the database class

// Set headers for CORS and content type
// Set headers for CORS (Cross-Origin Resource Sharing) and content type
header('Content-Type: application/json'); // Ensure response is in JSON format
header('Access-Control-Allow-Origin: *'); // Allow all domains (or specify domain if needed)
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // Allow POST, GET, OPTIONS methods
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Allow these headers






if(isset($_POST['id'])){
    $id =   $_POST['id'];
    
    if(DB::delete('contact',['id' => $id])){
        echo json_encode(['message' => 'Contact deleted successfully']);
    }else{
        echo json_encode(['message' => "Contact not found for the $id"]);
    }
}else{
    echo json_encode(['message' => 'Bad request']);
}


?>
