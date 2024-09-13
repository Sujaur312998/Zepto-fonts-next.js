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

// // Validate required fields
if (!isset($data['groupTitle']) || !isset($data['fontGroup'])) {
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

$groupTitle = $data['groupTitle'];
$fontGroup = $data['fontGroup'];

// Prepare to update fonts
$updateFontQuery = "UPDATE own_font SET font_name = ?, font_title = ?, specific_size = ?, price_change = ? WHERE id = ?";
$stmt = $conn->prepare($updateFontQuery);

if ($stmt === false) {
    echo json_encode(['error' => 'Failed to prepare statement for updating fonts']);
    exit();
}

$updatedFont = [];
foreach ($fontGroup as $font) {
    // Debug: Check if all required fields are present
    if (!isset($font['fontGroup']) || !isset($font['font_name']) || !isset($font['font_title']) || !isset($font['specific_size']) || !isset($font['price_change'])) {
        $insertFontQuery = "INSERT INTO own_font (font_name, font_title, specific_size, price_change) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertFontQuery);

        if ($stmt === false) {
            echo json_encode(['error' => 'Failed to prepare statement for font']);
            exit();
        }
        if (isset($font['font_name'], $font['font_title'], $font['specific_size'], $font['price_change'])) {
            $fontName = $font['font_name'];
            $fontTitle = (int) $font['font_title'];
            $specificSize = (float) $font['specific_size'];
            $priceChange = (float) $font['price_change'];

            if ($stmt->bind_param("sidd", $fontName, $fontTitle, $specificSize, $priceChange) && $stmt->execute()) {
                $fontId = $conn->insert_id;
                // echo json_encode($fontID,"Line 63");

                $insertFontGroupQuery = "INSERT INTO font_groups (groupTitle, fontGroup) VALUES (?, ?)";
                $stmt = $conn->prepare($insertFontGroupQuery);

                if ($stmt === false) {
                    echo json_encode(['error' => 'Failed to prepare statement for font groups']);
                    exit();
                }
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
            } else {
                echo json_encode(['error' => 'Failed to insert font']);
                exit();
            }
        }
        // echo json_encode(['error' => 'Missing required font fields', 'font' => $font]);
    } else {
        $fontId = (int) $font['fontGroup'];
        $fontName = $font['font_name'];
        $fontTitle = (int) $font['font_title'];
        $specificSize = (float) $font['specific_size'];
        $priceChange = (float) $font['price_change'];

        if ($stmt->bind_param("siddi", $fontName, $fontTitle, $specificSize, $priceChange, $fontId) && $stmt->execute()) {
            $updatedFont[] = [
                'fontId' => $fontId,
                'status' => 'Font updated successfully'
            ];
        } else {
            echo json_encode(['error' => 'Failed to update font', 'query' => $updateFontQuery]);
            exit();
        }
    }

}
$stmt->close();

// Prepare to update font groups
$updateFontGroupQuery = "UPDATE font_groups SET groupTitle = ? WHERE id = ?";
$stmtGroup = $conn->prepare($updateFontGroupQuery);

if ($stmtGroup === false) {
    echo json_encode(['error' => 'Failed to prepare statement for updating font groups']);
    exit();
}

$response = [];

foreach ($updatedFont as $font) {
    if (!isset($groupTitle)) {
        echo json_encode(['error' => 'Missing required font group fields']);
        exit();
    }
    $fontId = (int) $font['fontId'];

    if ($stmtGroup->bind_param('si', $groupTitle, $fontId) && $stmtGroup->execute()) {
        $response[] = [
            "fontGroup" => $fontId,
            'status' => 'Font Group updated successfully'
        ];
    } else {
        // Optionally handle the case where the query fails
        $response[] = [
            "fontGroup" => $fontId,
            'status' => 'Failed to update Font Group'
        ];
    }
}
$stmtGroup->close();


// echo json_encode($response );

$conn->close();

echo json_encode(['success' => 'update', 'response' => $response]);
?>