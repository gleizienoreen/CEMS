<?php  
session_start();
include 'connect.php';

if (isset($_GET['id'])) {
    $eventId = $_GET['id'];

    // Fetch the event details from the database
    $sqlFetchEvent = "SELECT * FROM events WHERE event_id = $eventId";
    $result = $conn->query($sqlFetchEvent);

    if ($result->num_rows === 1) {
        $event = $result->fetch_assoc();
    } else {
        echo "<p>Event not found.</p>";
        exit;
    }
} else {
    echo "<p>Invalid event ID.</p>";
    exit;
}
    $message='';
// Update the event if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventTitle = $_POST['event_title'];
    $eventDescription = $_POST['event_description'];
    $eventDate = $_POST['event_date'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $location = $_POST['location'];
    $targetAudience = $_POST['target_audience'];
    $specificCourse = $_POST['specific_course'];
    $registrationLimit = $_POST['registration_limit'];
    $eventType = $_POST['event_type'];
    $eventStatus = $_POST['event_status'];
    $additionalNotes = $_POST['additional_notes'];

    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $imagePath = 'uploads/' . basename($_FILES['image_path']['name']);
        move_uploaded_file($_FILES['image_path']['tmp_name'], $imagePath);
    } else {
        $imagePath = $event['image_path'];
    }


    $sqlUpdateEvent = "UPDATE events SET 
        event_title = '$eventTitle', 
        event_description = '$eventDescription', 
        event_date = '$eventDate', 
        start_time = '$startTime', 
        end_time = '$endTime', 
        location = '$location', 
        target_audience = '$targetAudience', 
        specific_course = '$specificCourse', 
        registration_limit = '$registrationLimit', 
        event_type = '$eventType', 
        event_status = '$eventStatus', 
        additional_notes = '$additionalNotes', 
        image_path = '$imagePath' 
        WHERE event_id = $eventId";

    if ($conn->query($sqlUpdateEvent) === TRUE) {
        $message = "<p class='success-message'>Event updated successfully.</p>";
    } else {
        $message = "<p style='color: red;'>Error updating event: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <style>
        body {   
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f7f7f7;
            color: #212c61; 
            margin: 0;
            padding: 20px;
        }

        h2 {
            color: #212c61; 
            text-align: center;
            margin-bottom: 15px; 
            font-size: 2rem; 
        }

        .container {
            max-width: 50%; 
            margin: 0 auto; 
            padding: 50px; 
            background-color: white; 
            border-radius: 12px; 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); 
        }

        form {
            display: flex;
            flex-direction: column; 
        }

        label {
            font-weight: bold;
            margin-bottom: 8px; 
            color: #212c61; 
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        input[type="number"],
        select,
        textarea {
            width: 100%; 
            padding: 12px; 
            margin-bottom: 20px; 
            border: 1px solid #ccc; 
            border-radius: 6px; 
            font-size: 16px; 
            transition: border-color 0.3s; 
            font-family: Arial, Helvetica, sans-serif;
        }

        input[type="file"] {
            margin-bottom: 20px; 
        }

        textarea {
            height: 100px; 
            resize: vertical; 
        }

        button {
            background-color: #212c61; 
            color: white; 
            padding: 14px 20px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 16px; 
            transition: background-color 0.3s, transform 0.3s; 
        }

        button:hover {
            background-color: #ffd000; 
            transform: translateY(-2px); 
        }

        a {
            display: block; 
            text-align: center; 
            margin-top: 25px; 
            text-decoration: none; 
            color: #212c61; 
            font-size: 16px; 
        }

        a:hover {
            text-decoration: underline; 
        }
        .success-message, .error-message {
            background-color: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb; 
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            text-align: center; 
            opacity: 1;
            transition: opacity 0.5s ease; 
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 30px; 
            }

            h2 {
                font-size: 1.5rem; 
            }

            button {
                font-size: 14px; 
            }
        }
    </style>
</head>
<body>
    <h2>Edit Event</h2>
    <div class="container">
        <form action="edit_event.php?id=<?= $eventId ?>" method="post" enctype="multipart/form-data">
            <?= $message; ?> 
            <label for="event_title">Event Title:</label>
            <input type="text" id="event_title" name="event_title" required value="<?= ($event['event_title']); ?>">

            <label for="event_description">Event Description:</label>
            <textarea id="event_description" name="event_description" required><?= ($event['event_description']); ?></textarea>

            <label for="event_date">Event Date:</label>
            <input type="date" id="event_date" name="event_date" min="<?= date('Y-m-d'); ?>" required value="<?= ($event['event_date']); ?>">

            <label for="start_time">Start Time:</label>
            <input type="time" id="start_time" name="start_time" required value="<?= ($event['start_time']); ?>">

            <label for="end_time">End Time:</label>
            <input type="time" id="end_time" name="end_time" required value="<?= ($event['end_time']); ?>">

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required value="<?= ($event['location']); ?>">

            <label for="target_audience">Target Audience:</label>
            <select id="target_audience" name="target_audience" required>
                <option value="All Students" <?= $event['target_audience'] === 'All Students' ? 'selected' : ''; ?>>All Students</option>
                <option value="Specific Course" <?= $event['target_audience'] === 'Specific Course' ? 'selected' : ''; ?>>Specific Course</option>
            </select>

            <label for="specific_course">Specific Course: (Optional)</label>
            <select id="specific_course" name="specific_course" value="<?= ($event['specific_course']); ?>">
                <option value="">--Select Course--</option>
                <option value="Bachelor of Elementary Education">Bachelor of Elementary Education</option>
                <option value="Bachelor of Secondary Education">Bachelor of Secondary Education</option>
                <option value="Bachelor of Science in Accountancy">Bachelor of Science in Accountancy</option>
                <option value="Bachelor of Science in Business Administration">Bachelor of Science in Business Administration</option>
                <option value="Bachelor of Science in Civil Engineering">Bachelor of Science in Civil Engineering</option>
                <option value="Bachelor of Science in Criminology">Bachelor of Science in Criminology</option>
                <option value="Bachelor of Science in Information Technology">Bachelor of Science in Information Technology</option>
                <option value="Bachelor of Science in Nursing">Bachelor of Science in Nursing</option>
            </select>


            <label for="registration_limit">Registration Limit:</label>
            <input type="number" id="registration_limit" name="registration_limit" required value="<?= ($event['registration_limit']); ?>">

            <label for="event_type">Event Type:</label>
            <input type="text" id="event_type" name="event_type" required value="<?= ($event['event_type']); ?>">

            <label for="event_status">Event Status:</label>
            <select id="event_status" name="event_status" required>
                <option value="Upcoming" <?= $event['event_status'] === 'Upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                <option value="Ongoing" <?= $event['event_status'] === 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                <option value="Completed" <?= $event['event_status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="Cancelled" <?= $event['event_status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>

            <label for="additional_notes">Additional Notes:</label>
            <textarea id="additional_notes" name="additional_notes"><?= ($event['additional_notes']); ?></textarea>

            <label for="image_path">Upload Image:</label>
            <input type="file" id="image_path" name="image_path">

            <button type="submit">Update Event</button>
        </form>
        <a href="http://localhost:3000/admin_dashboard.php?section=manage_events">Back to Events</a> <!-- Corrected Link -->
    </div>
</body>
</html>
