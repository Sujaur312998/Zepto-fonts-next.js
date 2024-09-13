<?php
// Set CORS headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/plain');

// Include the database class
require './Database.php';

// Create a new database connection
$db = new Database();

// Define table names
$uploaded_font = "uploaded_font";
$own_font = "own_font";  // You missed defining this variable
$font_groups = "font_groups";

// Define table creation queries
$create_uploaded_font_table = "CREATE TABLE $uploaded_font (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL
)";

$create_own_font = "CREATE TABLE $own_font (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    font_name VARCHAR(30) NOT NULL, 
    font_title INT(6) UNSIGNED,
    specific_size FLOAT NOT NULL,
    price_change FLOAT NOT NULL,

    FOREIGN KEY (font_title) REFERENCES $uploaded_font(id)
)";

$create_font_groups_table = "CREATE TABLE $font_groups (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    groupTitle VARCHAR(30) NOT NULL,
    fontGroup INT(6) UNSIGNED NOT NULL,
    
    FOREIGN KEY (fontGroup) REFERENCES $own_font(id)
)";

// Create tables if they do not exist
$db->createTableIfNotExists($uploaded_font, $create_uploaded_font_table);
$db->createTableIfNotExists($own_font, $create_own_font);
$db->createTableIfNotExists($font_groups, $create_font_groups_table);

// Close the database connection
$db->close();
?>
