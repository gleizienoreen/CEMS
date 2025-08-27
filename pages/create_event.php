<?php 
session_start();
include('connect.php');

if (isset($_POST['submit'])) {
    
    $event_title = $_POST['event_title'];
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $location = $_POST['location'];
    $target_audience = $_POST['target_audience'];
    $specific_course = $_POST['specific_course'] ?? null;
    $registration_limit = $_POST['registration_limit'] ?? null; 
    $event_type = $_POST['event_type'] ?? null;
    $event_status = $_POST['event_status'];
    $additional_notes = $_POST['additional_notes'] ?? null;

    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_path = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    
    $today = date('Y-m-d');
    if ($event_date < $today) {
        $_SESSION['message'] = "<div class='alert error'>Invalid event date. Please select a future date.</div>";
    } else {
    
        $sql = "INSERT INTO events (event_title, event_description, event_date, start_time, end_time, location, target_audience, specific_course, registration_limit, event_type, event_status, additional_notes, image_path)
                VALUES ('$event_title', '$event_description', '$event_date', '$start_time', '$end_time', '$location', '$target_audience', '$specific_course', '$registration_limit', '$event_type', '$event_status', '$additional_notes', '$image_path')";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "<div class='alert success'>New event created successfully.</div>";
            header("Location: admin_dashboard.php?section=create_events&status=success");
            exit();
        } else {
            $_SESSION['message'] =  "<div class ='alert error'>Error: " . $sql . "<br>" . $conn->error . "</div>";
        }
    }

    $conn->close();
}
?>
