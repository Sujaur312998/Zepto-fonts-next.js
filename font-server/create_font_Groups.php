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

// Check if the JSON decoding was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'Invalid JSON input']);
    exit();
}

// Validate required fields
if (!isset($data['groupTitle']) || !isset($data['fontGroup'])) {
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

$groupTitle = $data['groupTitle'];
$fontGroup = $data['fontGroup'];

// Insert into own_font table
$insertFontQuery = "INSERT INTO own_font (font_name, font_title, specific_size, price_change) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($insertFontQuery);

if ($stmt === false) {
    echo json_encode(['error' => 'Failed to prepare statement for font']);
    exit();
}

$fontGroupIds = [];
foreach ($fontGroup as $font) {
    if (isset($font['font_name'], $font['font_title'], $font['specific_size'], $font['price_change'])) {
        $fontName = $font['font_name'];
        $fontTitle = (int) $font['font_title'];
        $specificSize = (float) $font['specific_size'];
        $priceChange = (float) $font['price_change'];

        if ($stmt->bind_param("sidd", $fontName, $fontTitle, $specificSize, $priceChange) && $stmt->execute()) {
            $fontId = $conn->insert_id;
            $fontGroupIds[] = $fontId;
        } else {
            echo json_encode(['error' => 'Failed to insert font']);
            exit();
        }
    } else {
        echo json_encode(['error' => 'Missing required font fields']);
        exit();
    }
}

// Close the statement for the first query
$stmt->close();

// Insert into font_groups table
$insertFontGroupQuery = "INSERT INTO font_groups (groupTitle, fontGroup) VALUES (?, ?)";
$stmt = $conn->prepare($insertFontGroupQuery);

if ($stmt === false) {
    echo json_encode(['error' => 'Failed to prepare statement for font groups']);
    exit();
}

$response = [];
foreach ($fontGroupIds as $fontId) {
    if ($stmt->bind_param("si", $groupTitle, $fontId) && $stmt->execute()) {
        $groupID = $conn->insert_id;
        $response[] = [
            'fontId' => $fontId,
            'groupID' => $groupID
        ];
    } else {
        echo json_encode(['error' => 'Failed to execute statement for font groups']);
        exit();
    }
}

// Close the statement and connection
$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'response' => $response]);
?>
