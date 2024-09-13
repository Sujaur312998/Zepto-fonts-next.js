<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET");
header('Content-Type: application/json');

// Include the Database class
require './DB/Database.php';

// Create a new database connection
$db = new Database();
$connection = $db->getConnection(); // Get the connection using the public method

$response = [];

// Query to get all fonts
$query = "SELECT * FROM uploaded_font";
$result = $connection->query($query);

if ($result) {
    // Fetch all rows
    $fonts = [];
    while ($row = $result->fetch_assoc()) {
        $fonts[] = $row;
    }

    // Set response
    $response['status'] = 'success';
    $response['fonts'] = $fonts;
} else {
    // Handle query error
    $response['status'] = 'error';
    $response['message'] = 'Error retrieving fonts: ' . $connection->error;
}

// Close the database connection
$db->close();

// Output the JSON response
echo json_encode($response);
?>
