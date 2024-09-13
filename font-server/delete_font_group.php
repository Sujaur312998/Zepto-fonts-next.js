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


if (!isset($data['fontGroup']) || !isset($data['groupTitle'])) {
    echo json_encode(['error' => 'Invalid input: fontGroup is required']);
    exit();
}

$groupTitle = $data['groupTitle'];
$fontGroup = $data['fontGroup'];

$deleteGroupFontQuery = "DELETE FROM font_groups WHERE groupTitle = ?";
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
$stmtFontGroup->close();


$deleteOwnFontQuery = "DELETE FROM own_font WHERE id = ?";
$stmtOwnFont = $conn->prepare($deleteOwnFontQuery);

if ($stmtOwnFont === false) {
    echo json_encode(['error' => 'Failed to prepare statement for deleting font group']);
    exit();
}

$fontGroupIds = [];
foreach ($fontGroup as $font) {
    if (!$font['fontGroup']) {
        echo json_encode(['error' => 'Failed to get own fontid']);
        exit();
    }
    $id = (int) $font['fontGroup'];
    $stmtOwnFont->bind_param('i', $id);
    $resultOwnFont = $stmtOwnFont->execute();

    if ($resultOwnFont === false) {
        echo json_encode(['error' => 'Failed to execute statement for deleting Own font group']);
        exit();
    }
}
$stmtOwnFont->close();
echo json_encode($resultOwnFont)

?>