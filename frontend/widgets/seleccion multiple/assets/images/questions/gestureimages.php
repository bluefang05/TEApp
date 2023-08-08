<?php
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Database connection details
$db_host = "sql212.epizy.com";
$db_user = "epiz_33977615";
$db_password = "r7NJibfUlaP3S89";
$db_name = "epiz_33977615_widgets";


// Connect to the database
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die(json_encode(array("error" => "Database connection failed: " . $conn->connect_error)));
}

// Get the number of random rows requested
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5; // Default limit is set to 5

// Fetch random rows from the table with specific columns
$sql = "SELECT id, url, answer FROM FacialGestures ORDER BY RAND() LIMIT $limit";
$result = $conn->query($sql);

// Process the results and output them in JSON format with custom structure
if ($result) {
    if ($result->num_rows > 0) {
        $rows = array();
        while($row = $result->fetch_assoc()) {
            $rows[] = array(
                "id" => $row['id'],
                "data" => array(
                    "url" => $row['url'],
                    "answer" => $row['answer']
                )
            );
        }
        echo json_encode($rows);
    } else {
        echo json_encode(array("error" => "No data found"));
    }
} else {
    echo json_encode(array("error" => "Query execution failed: " . $conn->error . ". Query: " . $sql));
}

$conn->close();
?>
