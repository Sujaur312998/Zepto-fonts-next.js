<?php
// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST");
header('Content-Type: application/json');

// Include the database class
require './DB/Database.php';

// Create a new database connection
$db = new Database();
$conn = $db->getConnection();

// Get the JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (!isset($data['fontGroup'])) {
    echo json_encode(['error' => 'Invalid input: fontGroup is required']);
    exit();
}

$groupTitle = $data['fontGroup'];

// Prepare and execute deletion query for font group
$deleteGroupFontQuery = "DELETE FROM font_groups WHERE fontGroup = ?";
$stmtFontGroup = $conn->prepare($deleteGroupFontQuery);
if ($stmtFontGroup === false) {
    echo json_encode(['error' => 'Failed to prepare statement for deleting font group']);
    exit();
}

$stmtFontGroup->bind_param('s', $groupTitle);
$resultFontGroup = $stmtFontGroup->execute();

if ($resultFontGroup === false) {
    echo json_encode(['error' => 'Failed to execute statement for deleting font group']);
    exit();
}

// Prepare and execute deletion query for associated fonts
$deleteOwnFontQuery = "DELETE FROM own_font WHERE id = ?";
$stmtOwnFont = $conn->prepare($deleteOwnFontQuery);
if ($stmtOwnFont === false) {
    echo json_encode(['error' => 'Failed to prepare statement for deleting associated fonts']);
    exit();
}

$stmtOwnFont->bind_param('s', $groupTitle);
$resultOwnFont = $stmtOwnFont->execute();
if ($resultOwnFont === false) {
    echo json_encode(['error' => 'Failed to execute statement for deleting associated fonts']);
    exit();
}

// Close statements
$stmtFontGroup->close();
$stmtOwnFont->close();

// Close connection
$conn->close();

// Respond with success
echo json_encode(['success' => true]);
?>