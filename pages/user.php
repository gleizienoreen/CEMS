<?php
session_start();
include 'connect.php'; 

$email = $_SESSION['student_email'];


if (isset($_SESSION['success_message'])) {
    echo "<script>alert('" . $_SESSION['success_message'] . "');</script>";
    unset($_SESSION['success_message']); 
}

if (isset($_SESSION['error_message'])) {
    echo "<script>alert('" . $_SESSION['error_message'] . "');</script>";
    unset($_SESSION['error_message']); 
}
if (isset($_SESSION['message'])) {
    echo "<div class='alert'>" . $_SESSION['message'] . "</div>";
    unset($_SESSION['message']); 
}

if (!isset($_SESSION['student_email'])) {
    header("Location: login-form.php");
    exit;
}

$currentSection = 'events';

if (isset ($_GET['section'])){
    $currentSection = $_GET['section'];
}


$student_id = $_SESSION['student_id'];
$email = $_SESSION['student_email'];
$sql = "SELECT first_name, last_name, course, section FROM students WHERE email = '$email'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

$fullName = $user ? ($user['first_name'] . ' ' . $user['last_name']) : 'User';


$sql = "SELECT r.registration_id, e.event_title, r.registration_status, r.registration_date
        FROM registration r 
        JOIN events e ON r.event_id = e.event_id 
        WHERE r.student_id = '$student_id'";
$result = mysqli_query($conn, $sql);

$sqlHistory = "SELECT r.registration_id, e.event_title, r.registration_date, e.event_date, e.event_status  
               FROM registration r
               JOIN events e ON r.event_id = e.event_id
               JOIN students s ON r.student_id = s.student_id
               WHERE s.email = '$email' AND e.event_status = 'Completed'"; 

$resultHistory = $conn->query($sqlHistory);

$query = "SELECT event_id, event_title, event_description, event_date, start_time, end_time,  
                 location, target_audience, specific_course, registration_limit, event_status, image_path,
                 (SELECT COUNT(*) FROM registration r WHERE r.event_id = e.event_id AND r.registration_status = 'Approved') AS registered_count
          FROM events e
          WHERE e.event_status = 'Upcoming'";
$resultEvents = $conn->query($query);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_id = $_POST['registration_id'];
    
    $sql = "UPDATE registration SET registration_status = 'cancelled' WHERE registration_id = '$registration_id'";
    mysqli_query($conn, $sql);

    header("Location: user.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="student.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
</head>
<body>
    <div class="container">

        <aside class="sidebar" id="sidebar">
            <div class="logo">
                <h1>Campus Event Management System</h1>
                <img src="images/logo.png" alt="Logo"> 
            </div>
            
            <nav class="menu">
                <ul>
                    <li><span class="material-symbols-outlined">event_upcoming</span><a href="?section=events">Events</a></li>
                    <li><span class="material-symbols-outlined">account_circle</span><a href="?section=profile-info">Profile Information</a></li>
                    <li><span class="material-symbols-outlined">history</span><a href="?section=history">History</a></li>
                    <li><span class="material-symbols-outlined">pending_actions</span><a href="?section=registrations">Registrations</a></li>
                </ul>
            </nav>
        </aside>

        <div class="main-content">

            <header class="top-bar">
                <img src="images/menu-icon.png" alt="Menu Icon" class="menu-icon" id="menu-icon"> 
                    <div class="profile">
                            <span class="profile-name"><?php echo ($_SESSION['student_name'] ?? 'Admin'); ?></span> 
                            <span class="dropdown-icon material-symbols-outlined" id="dropdown-icon">arrow_drop_down</span> 

                            <div class="dropdown-menu" id="dropdown-menu" style="display: none;"> 
                            <a href="logout.php">Log Out</a> 
                    </div>
                </div>
            </header>


<section id="events" style="<?= $currentSection === 'events' ? 'display:block;' : ''; ?>" class="events"> 
    <h2>Available Events</h2>
    
    <div class="event-container"> 
        <?php
        if ($resultEvents && $resultEvents->num_rows > 0) {
            while ($row = $resultEvents->fetch_assoc()) {
                $event_id = ($row['event_id']);
                $title = ($row['event_title']);
                $description = ($row['event_description']);
                $date = date('F j, Y', strtotime($row['event_date']));
                $start_time = date('g:i a', strtotime($row['start_time']));
                $end_time = date('g:i a', strtotime($row['end_time']));
                $location = ($row['location']);
                $target_audience = ($row['target_audience']);
                $registered_count = ($row['registered_count']);
                $registration_limit = ($row['registration_limit']);
                $image_path = ($row['image_path']); 
                ?>
                <div class='event-card'>
                    <div class='event-content'>
                        <?php if ($image_path): ?> 
                            <img src="<?php echo $image_path; ?>" alt="<?php echo $title; ?>" class="event-image">
                        <?php endif; ?>
                        <div class="event-details">
                            <h3><?php echo $title; ?></h3>
                            <p class="event-description"><?php echo $description; ?></p>
                            <p><strong>Date:</strong> <?php echo $date; ?> from <?php echo $start_time; ?> to <?php echo $end_time; ?></p>
                            <p><strong>Location:</strong> <?php echo $location; ?></p>
                            <p><strong>Who can join:</strong> <?php echo $target_audience; ?></p>
                            <p><strong>Registered:</strong> <?php echo $registered_count; ?> / <?php echo $registration_limit; ?></p>
                            <div class="event-actions"> 
                                <form action='event_registration.php' method='POST' onsubmit="return confirm('Are you sure you want to register for this event?')">
                                    <input type='hidden' name='event_id' value='<?php echo $event_id; ?>'> 
                                    <button type='submit' class='register-button'>Register</button>
                                </form>
                                <a href="event_details.php?event_id=<?php echo $event_id; ?>">View Event Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>No upcoming events found.</p>";
        }
        ?>
    </div> 
</section>

            
            <section id="profile-info" style="<?= $currentSection==='profile-info' ? 'display:block;' : ''; ?>" class="section profile-info-section">
                <h2>Profile Information</h2>
                <form action="update_profile.php" method="POST" class="profile-form">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" name="first_name" id="first_name" value="<?php echo ($user['first_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" name="last_name" id="last_name" value="<?php echo ($user['last_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" value="<?php echo ($email); ?>" required readonly>
                    </div>

                    <div class="form-group">
                        <label for="section">Section:</label>
                        <input type="text" name="section" id="section" value="<?php echo ($user['section']); ?>" placeholder="e.g., BSBA2-SOUTH1" required>
                    </div>
                    <div class="form-group">
                        <label for="password">New Password:</label>
                        <input type="password" name="password" id="password" placeholder="Enter new password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password">
                    </div>
                    <button type="submit" class="btn-update">Update Profile</button>
                </form>
            </section>


            <section id="history" style="<?= $currentSection==='history' ? 'display:block;' : ''; ?>" class="section">
                <h2>Event History</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Registration ID</th>
                            <th>Event Name</th>
                            <th>Registration Date</th>
                            <th>Event Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultHistory && $resultHistory->num_rows > 0): ?>
                            <?php while ($row = $resultHistory->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo ($row["registration_id"]); ?></td>
                                    <td><?php echo ($row["event_title"]); ?></td>
                                    <td><?php echo ($row["registration_date"]); ?></td>
                                    <td><?php echo ($row["event_date"]); ?></td>
                                    <td><?php echo ($row["event_status"]); ?></td> 
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan='5'>No event history found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

        
<section id="registrations" style="<?= $currentSection === 'registrations' ? 'display:block;' : 'display:none;'; ?>" class="registrations section">
    <h2>Your Event Registrations</h2>
    <table>
        <tr>
            <th>Event Name</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        <?php if (mysqli_num_rows($result) > 0): ?> 
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= ($row['event_title']) ?></td>
                    <td><?= ($row['registration_status']) ?></td>
                    <td><?= ($row['registration_date']) ?></td>
                    <td>
                        <?php
                        if ($row['registration_status'] === 'Pending') {
                        ?>
                            <form method="POST" action="cancel_registration.php" style="display:inline;">
                                <input type="hidden" name="registration_id" value="<?= ($row['registration_id']) ?>">
                                <button type="submit" onclick="return confirm('Are you sure you want to cancel this registration?');">Cancel</button>
                            </form>
                        <?php
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?> 
            <tr><td colspan='4'>No event registrations found.</td></tr>
        <?php endif; ?>
    </table>
</section>


    </div>
    <script>
        document.getElementById('menu-icon').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        });
        document.getElementById('dropdown-icon').addEventListener('click', function() {
        const dropdownMenu = document.getElementById('dropdown-menu');
        dropdownMenu.style.display = dropdownMenu.style.display === 'none' ? 'block' : 'none';
        });
    </script>
</body>
</html>
