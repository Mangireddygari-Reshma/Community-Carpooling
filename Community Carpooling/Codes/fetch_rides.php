<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carpooling";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    echo json_encode(["error" => "Database connection failed."]);
    exit();
}

$destination_location = isset($_GET['destination_location']) ? $conn->real_escape_string($_GET['destination_location']) : '';
$ride_date = isset($_GET['ride_date']) ? $conn->real_escape_string($_GET['ride_date']) : '';

error_log("Fetching rides for destination: $destination_location, date: $ride_date");

if (empty($destination_location) || empty($ride_date)) {
    echo json_encode(["error" => "Destination location and ride date are required."]);
    exit();
}

$sql = "SELECT ride_id, origin, destination, time, available_seats 
        FROM rides 
        WHERE destination='$destination_location' AND date='$ride_date' AND available_seats > 0";
error_log("SQL Query: " . $sql);

$result = $conn->query($sql);

if (!$result) {
    error_log("SQL error: " . $conn->error);
    echo json_encode(["error" => "Database query failed: " . $conn->error]);
    exit();
}

$rides = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rides[] = $row;
    }
} else {
    error_log("No rides found for destination: $destination_location, date: $ride_date");
    echo json_encode([]);
    exit();
}

echo json_encode($rides);

$conn->close();
?>
