<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST");
header('Content-Type: application/json');

// Include the Database class
require './DB/Database.php';

// Define upload directory
$uploadDirectory = '../../../Users/suja3/OneDrive/Desktop/font_app/public/fonts/';
// Get the absolute path of the upload directory
$absoluteUploadDirectory = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . $uploadDirectory;

// Make sure upload directory exists
if (!is_dir($absoluteUploadDirectory)) {
    mkdir($absoluteUploadDirectory, 0777, true);
}

$response = [];

// Create a new database connection
$db = new Database();
$connection = $db->getConnection(); // Get the connection using the public method

// Check if a file is uploaded
if (isset($_FILES['fontFile'])) {
    $file = $_FILES['fontFile'];

    // Get file info
    $fileName = basename($file['name']);
    $targetFilePath = $uploadDirectory . $fileName;
    $absoluteFilePath = $absoluteUploadDirectory  . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Check if file is a .ttf file
    if ($fileType === "ttf") {
        // Move file to the upload directory
        if (move_uploaded_file($file['tmp_name'], $absoluteFilePath)) {
            $response['message'] = "File uploaded successfully";
            $response['fileName'] = $fileName;
            $response['filePath'] = $targetFilePath; // relative path for front-end

            // Insert file information into the database
            $stmt = $connection->prepare("INSERT INTO uploaded_font (name) VALUES (?)");
            $stmt->bind_param("s", $fileName);

            if ($stmt->execute()) {
                $response['dbMessage'] = "Data inserted into database successfully";
            } else {
                $response['dbError'] = "Error inserting data into database: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $response['error'] = "Error moving file";
        }
    } else {
        $response['error'] = "Only .ttf files are allowed";
    }
} else {
    $response['error'] = "No file uploaded";
}

// Close the database connection
$db->close();

// Output the JSON response
echo json_encode($response);
?>
