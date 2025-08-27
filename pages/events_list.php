<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['admin_email'])) {
    header("Location: login-form.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events List</title>
    <link rel="stylesheet" href="admin.css">
    <style>
            body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: left;
            margin-bottom: 20px;
            color: #212c61;
        }
        a { 
            display: block; 
            text-align: center; 
            border-radius: 5px;
            padding: 10px;
            width: 200px; 
            margin: 10px auto; 
            background-color: #212c61; 
            text-decoration: none; 
            color: #f7f7f7; 
            font-size: 16px;
        }   

        a:hover {
        background-color: #333;
        }

    </style>
</head>
<body>
<h2>Events List</h2>
<table>
    <thead>
        <tr>
            <th>Event Name</th>
            <th>Date</th>
            <th>Location</th>
            <th>Total Attendees</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT event_id, event_title, event_date, location FROM events";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . ($row['event_title']) . "</td>";
            echo "<td>" . ($row['event_date']) . "</td>";
            echo "<td>" . ($row['location']) . "</td>";

            $attendeeQuery = "SELECT COUNT(*) AS total_attendees FROM registration WHERE event_id = " . (int)$row['event_id'] . " AND registration_status = 'Approved'";
            $attendeeResult = mysqli_query($conn, $attendeeQuery);
            $attendeeCount = mysqli_fetch_assoc($attendeeResult);
            echo "<td>" . ($attendeeCount['total_attendees']) . "</td>";

            echo "</tr>";
        }
        ?>
    </tbody>
</table>
        <a href="http://localhost:3000/admin_dashboard.php?section=dashboard">Back to the Dashboard</a>
</body>
</html>
