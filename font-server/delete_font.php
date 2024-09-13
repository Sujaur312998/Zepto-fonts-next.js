<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST");
header('Content-Type: application/json');

// Include the Database class
require './DB/Database.php';

// Retrieve font ID from POST request
$data = json_decode(file_get_contents('php://input'), true);
$fontId = isset($data['id']) ? intval($data['id']) : 0;

$response = [];

$db = new Database();
$conn = $db->getConnection();

$sql = "SELECT * FROM own_font WHERE font_title = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $fontId);
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $fonts = $result->fetch_all(MYSQLI_ASSOC);
    if (!empty($fonts)) {
        $response = $fonts;
    }
}
$stmt->close();

// Delete from Font Group Table Data First
$sql = "DELETE FROM font_groups WHERE fontGroup = ?";
$deleteFontGroupstmt = $conn->prepare($sql);

foreach ($response as $entry) {
    $fontGroup = $entry['id'];
    $deleteFontGroupstmt->bind_param('i', $fontGroup);
    $resultFontGroup = $deleteFontGroupstmt->execute();
}
$deleteFontGroupstmt->close();

// Delete From Own Fonts Table Data Secondly
$sql = "DELETE FROM own_font WHERE font_title = ?";
$stmtOwnFont = $conn->prepare($sql);
$stmtOwnFont->bind_param('i', $fontId);
$resultOwnFont = $stmtOwnFont->execute();

$stmtOwnFont->close();

// Delete Form Upload Font Table Data Finally
$sql = "DELETE FROM uploaded_font WHERE id = ?";
$stmtUploadedFont = $conn->prepare($sql);
$stmtUploadedFont->bind_param('i', $fontId);
$resultUploadedFont = $stmtUploadedFont->execute();

if (!$resultUploadedFont) {
    echo json_encode(['error' => 'Failed to prepare statement for deleting font group']);
    exit();
}
echo json_encode($resultUploadedFont);
$stmtUploadedFont->close();


    // // Prepare to fetch font info
// $stmt = $connection->prepare("SELECT name, location FROM uploaded_font WHERE id = ?");
// $stmt->bind_param("i", $fontId);
// $stmt->execute();
// $result = $stmt->get_result();

    // if ($result->num_rows > 0) {
//     $font = $result->fetch_assoc();
//     $fileName = $font['name'];
//     $filePath = $font['location'];

    //     // Delete the file from the server
//     if (file_exists($filePath)) {
//         if (unlink($filePath)) {
//             // Delete the record from the database
//             $stmt = $connection->prepare("DELETE FROM uploaded_font WHERE id = ?");
//             $stmt->bind_param("i", $fontId);

    //             if ($stmt->execute()) {
//                 $response['status'] = 'success';
//                 $response['message'] = 'Font deleted successfully';
//             } else {
//                 $response['status'] = 'error';
//                 $response['message'] = 'Error deleting record from database: ' . $stmt->error;
//             }
//         } else {
//             $response['status'] = 'error';
//             $response['message'] = 'Error deleting file from server';
//         }
//     } else {
//         $response['status'] = 'error';
//         $response['message'] = 'File does not exist';
//     }

    //     $stmt->close();
// } else {
//     $response['status'] = 'error';
//     $response['message'] = 'Font not found';
// }

    // // Close the database connection
// $db->close();

    // // Output the JSON response
// echo json_encode($response);
?>