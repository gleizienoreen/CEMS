<?php
$servername = "localhost";
$username = "root";
$password = ""; // Change if you've set a password
$database = "event_management";
$port = 3307; // Updated port


$conn = new mysqli($servername, $username, $password, $database, $port);


if ($conn->connect_error) {
    echo("Connection failed: " . $conn->connect_error);
}

//echo "Connected successfully";

?>
