<?php
// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET");
header('Content-Type: application/json');

// Include the database class
require './DB/Database.php';

// Create a new database connection
$db = new Database();
$conn = $db->getConnection();

$response = [];

// Query to get font groups
$fontGroupsQuery = "SELECT * FROM `font_groups`";
$fontGroupsResult = $conn->query($fontGroupsQuery);

if ($fontGroupsResult) {
    // Fetch all font groups
    $fontGroups = $fontGroupsResult->fetch_all(MYSQLI_ASSOC);

    // Prepare to store detailed information
    foreach ($fontGroups as &$fontGroup) {
        $fontGroupId = $fontGroup['fontGroup'];

        // Query to get font details for each font group
        $fontDetailsQuery = "SELECT * FROM `own_font` WHERE id = ?";
        $stmt = $conn->prepare($fontDetailsQuery);
        $stmt->bind_param('i', $fontGroupId); // assuming fontGroup is an integer
        $stmt->execute();
        $fontDetailsResult = $stmt->get_result();

        if ($fontDetailsResult) {
            $fontGroup['details'] = $fontDetailsResult->fetch_all(MYSQLI_ASSOC);
        } else {
            $fontGroup['details'] = ["error" => $conn->error];
        }

        $stmt->close();
    }

    $response = $fontGroups;
} else {
    $response = ["error" => $conn->error];
}

// Process the data
$result = [];

foreach ($response as $entry) {
    $groupTitle = $entry['groupTitle'];
    if (!isset($result[$groupTitle])) {
        $result[$groupTitle] = [
            'id'=> $entry['id'],
            "groupTitle" => $groupTitle,
            "fontGroup" => []
        ];
    }
    
    foreach ($entry['details'] as $detail) {
        $result[$groupTitle]['fontGroup'][] = [
            "fontGroup" => $entry['fontGroup'],
            "font_name" => $detail['font_name'],
            "font_title" => (string)$detail['font_title'],
            "specific_size" => (string)$detail['specific_size'],
            "price_change" => (string)$detail['price_change']
        ];
    }
}

// Convert result to indexed array
$result = array_values($result);

// Output result in JSON format
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>
