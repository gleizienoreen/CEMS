<?php
session_start();
include 'connect.php'; 

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    

    $event_query = "SELECT * FROM events WHERE event_id = '$event_id'";
    $event_result = mysqli_query($conn, $event_query);
    
    if ($event_result && mysqli_num_rows($event_result) > 0) {
        $event = mysqli_fetch_assoc($event_result);
    
        $title = $event['event_title'];
        $description = $event['event_description'];
        $event_type = $event['event_type'];
        $additional_notes = $event['additional_notes'];
        $event_status = $event['event_status'];
        $course = $event['specific_course'];
        $date = date('F j, Y', strtotime($event['event_date']));
        $start_time = date('g:i a', strtotime($event['start_time']));
        $end_time = date('g:i a', strtotime($event['end_time']));
        $location = $event['location'];
        $target_audience = $event['target_audience'];
        $image_path = $event['image_path'];
    } else {
        echo "<p>Event not found.</p>";
    }
} else {
    echo "<p>No event selected.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Event Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .event-details-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s;
        }

        .event-details-container:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        }

        .event-title {
            color: #212c61; 
            font-size: 2em;
            margin-bottom: 20px;
        }

        .event-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .event-info p {
            margin: 10px 0;
            color: #555;
        }

        .event-info strong {
            color: #212c61; 
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ffd000;
            color: #212c61;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.3s;
        }

        .back-button:hover {
            background-color: #e6c700;
            transform: translateY(-2px);
        }

        @media (max-width: 600px) {
            .event-details-container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="event-details-container">
        <?php if (isset($event)): ?>
            <h2 class="event-title"><?php echo $title; ?></h2>
            <?php if ($image_path): ?>
                <img src="<?php echo $image_path; ?>" alt="<?php echo $title; ?>" class="event-image">
            <?php endif; ?>
            <div class="event-info">
                <p><strong>Description:</strong> <?php echo $description; ?></p>
                <p><strong>Event Type:</strong> <?php echo $event_type; ?></p>
                <p><strong>Additional Notes:</strong> <?php echo $additional_notes; ?></p>
                <p><strong>Status:</strong> <?php echo $event_status; ?></p>
                <?php if (!empty($course)): ?> 
                    <p><strong>Course:</strong> <?php echo $course; ?></p>
                <?php endif; ?>
                <p><strong>Date:</strong> <?php echo $date; ?> from <?php echo $start_time; ?> to <?php echo $end_time; ?></p>
                <p><strong>Location:</strong> <?php echo $location; ?></p>
                <p><strong>Who can join:</strong> <?php echo $target_audience; ?></p>
            </div>
        <?php else: ?>
            <p>Event not found.</p>
        <?php endif; ?>
        <a href="http://localhost:3000/user.php?section=events" class="back-button">Back to Events</a>
    </div>
</body>
</html>
