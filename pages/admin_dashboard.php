<?php 
session_start(); 
include 'connect.php';


if (!isset($_SESSION['admin_email'])) {
    header("Location: login-form.php"); 
    exit;
}
if (isset($_SESSION['message'])) {
    echo "<div class='alert'>" . $_SESSION['message'] . "</div>";
    unset($_SESSION['message']); 
}

$currentSection = 'dashboard';

if (isset($_GET['section'])) {
    $currentSection = $_GET['section'];
}

$totalEventsQuery = "SELECT COUNT(*) AS total FROM events"; 
$totalEventsResult = mysqli_query($conn, $totalEventsQuery);
$totalEvents = mysqli_fetch_assoc($totalEventsResult)['total'];

$totalStudentsQuery = "SELECT COUNT(*) AS total FROM students"; 
$totalStudentsResult = mysqli_query($conn, $totalStudentsQuery);
$totalStudents = mysqli_fetch_assoc($totalStudentsResult)['total'];

$totalAdminsQuery = "SELECT COUNT(*) AS total FROM admins"; 
$totalAdminsResult = mysqli_query($conn, $totalAdminsQuery);
$totalAdmins = mysqli_fetch_assoc($totalAdminsResult)['total'];

$totalAccounts = $totalStudents + $totalAdmins;


$pendingRegistrationsQuery = "SELECT COUNT(*) AS total FROM registration WHERE registration_status = 'pending'"; 
$pendingRegistrationsResult = mysqli_query($conn, $pendingRegistrationsQuery);
$pendingRegistrations = mysqli_fetch_assoc($pendingRegistrationsResult)['total'];
$sqlRegistrations = "SELECT r.registration_id, s.student_number, s.first_name, s.last_name, s.course, e.event_title, 
                            r.registration_date, e.event_date, r.registration_status
                     FROM registration r
                     JOIN students s ON r.student_id = s.student_id
                     JOIN events e ON r.event_id = e.event_id
                     WHERE r.registration_status != 'Approved'";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
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
                <li><span class="material-symbols-outlined">dashboard</span><a href="?section=dashboard">Dashboard</a></li>
            </ul>
            <h3>TRACK</h3>
            <ul>
                <li><span class="material-symbols-outlined">groups</span><a href="?section=attendee_lists">Attendee Lists</a></li>
                <li><span class="material-symbols-outlined">format_list_bulleted</span><a href="?section=admin_registrations">Registrations</a></li>
            </ul>
            <h3>TOOLS</h3>
            <ul>
                <li><span class="material-symbols-outlined">edit_calendar</span><a href="?section=create_events">Create Events</a></li>
                <li><span class="material-symbols-outlined">edit_square</span><a href="?section=manage_events">Manage Events</a></li>
                <li><span class="material-symbols-outlined">manage_accounts</span><a href="?section=manage_accounts">Manage Accounts</a></li>
            </ul>
        </nav>
    </aside>


    <div class="main-content">

        <header class="top-bar">
            <img src="images/menu-icon.png" alt="Menu Icon" class="menu-icon" id="menu-icon">
        <div class="profile">
            <span class="profile-name"><?php echo ($_SESSION['admin_name'] ?? 'Admin'); ?></span> 
            <span class="dropdown-icon material-symbols-outlined" id="dropdown-icon">arrow_drop_down</span> 

            <div class="dropdown-menu" id="dropdown-menu" style="display: none;">
            <a href="logout.php">Log Out</a> 
    </div>
</div>

        </header>

      
        <section class="sections" id="dashboard" style="<?= $currentSection === 'dashboard' ? 'display:block;' : 'display:none'; ?>">
            <h2>Welcome to the Admin Panel</h2>
            <p>Here you can manage events and view attendee registrations.</p>
            <section class="dashboard-cards">
                <div class="dashboard-cards">
               
                    <div class="card event-card">
                        <span class="material-symbols-outlined">event</span>
                        <div class="card-content">
                            <h3>Total Events</h3>
                            <p><?= $totalEvents; ?></p> 
                            <a href="events_list.php">View Details</a>
                        </div>
                    </div>

                    <div class="card accounts-card">
                        <span class="material-symbols-outlined">groups</span>
                        <div class="card-content">
                            <h3>Accounts</h3>
                            <p> <?= $totalAccounts; ?></p> 
                            <a href="http://localhost:3000/admin_dashboard.php?section=manage_accounts">View Details</a>
                        </div>
                    </div>

          
                    <div class="card pending-card">
                        <span class="material-symbols-outlined">hourglass_top</span>
                        <div class="card-content">
                            <h3>Pending Registrations</h3>
                            <p><?= $pendingRegistrations; ?></p> 
                            <a href="http://localhost:3000/admin_dashboard.php?section=admin_registrations">View Details</a>
                        </div>
                    </div>
                </div>
            </section>
        </section>



<section class="sections" id="attendee_lists" style="<?= $currentSection === 'attendee_lists' ? 'display:block;' : 'display:none'; ?>">
    <h2>Attendee Lists</h2>
    <form method="POST" action="">
        <label for="filter_event">Select Event:</label>
        <select name="filter_event" id="filter_event" required>
            <option value="">-- Choose an Event --</option>
            <?php
            
            $sqlEvents = "SELECT event_id, event_title FROM events ORDER BY event_date ASC";
            $resultEvents = $conn->query($sqlEvents);
            while ($row = $resultEvents->fetch_assoc()) {
                echo "<option value='" . ($row['event_id']) . "'>" . ($row['event_title']) . "</option>";
            }
            ?>
        </select>
        <button type="submit" name="filter_attendees">Filter</button>
    </form>

    <?php
    if (isset($_POST['filter_attendees'])) {
        $event_id = $_POST['filter_event'];
        $sqlAttendees = "SELECT s.student_id, s.first_name, s.last_name, s.email, s.course, s.section, r.registration_status 
                         FROM registration r 
                         JOIN students s ON r.student_id = s.student_id 
                         WHERE r.event_id = '$event_id' AND r.registration_status = 'approved'"; 

        $resultAttendees = $conn->query($sqlAttendees);

        if ($resultAttendees && $resultAttendees->num_rows > 0) {
            $counter = 1; 
            echo "<table>
                    <thead>
                        <tr>
                            <th>#</th> <!-- Column for attendee count -->
                            <th>Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Section</th> <!-- New column for section -->
                            <th>Status</th>
                            <th>Action</th> <!-- New column for action -->
                        </tr>
                    </thead>
                    <tbody>";
            while ($row = $resultAttendees->fetch_assoc()) {
                echo "<tr>
                        <td>" . $counter++ . "</td> <!-- Displaying the counter and incrementing it -->
                        <td>" . ($row['first_name'] . ' ' . $row['last_name']) . "</td>
                        <td>" . ($row['email']) . "</td>
                        <td>" . ($row['course']) . "</td>
                        <td>" . ($row['section']) . "</td> <!-- Displaying section -->
                        <td>" . ($row['registration_status']) . "</td>
                        <td>
                            <form method='POST' action=''>
                                <input type='hidden' name='student_id' value='" . ($row['student_id']) . "' />
                                <input type='hidden' name='event_id' value='" . ($event_id) . "' />
                                <button type='submit' name='remove_attendee'>Remove</button>
                            </form>
                        </td>
                      </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No attendees found for the selected event.</p>";
        }
    }
    
    if (isset($_POST['remove_attendee'])) {
        $student_id = $_POST['student_id'];
        $event_id = $_POST['event_id'];

        $sqlRemove = "DELETE FROM registration WHERE student_id = '$student_id' AND event_id = '$event_id'";
        if ($conn->query($sqlRemove) === TRUE) {
             "<p class = alert success>Attendee removed successfully.</p>";
        } else {
            echo "<p>Error removing attendee: " . $conn->error . "</p>";
        }
    }
    ?>
</section>




<section class="sections" id="admin_registrations" style="<?= $currentSection === 'admin_registrations' ? 'display:block;' : 'display:none'; ?>"> 
<h2>Student Registrations</h2> 
<table>
    <thead>
        <tr>
            <th>Student Number</th>
            <th>Student Name</th>
            <th>Course</th>
            <th>Email</th>
            <th>Event</th>
            <th>Registration Date</th>
            <th>Event Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sqlRegistrations = "SELECT r.registration_id, s.student_number, s.first_name, s.last_name, s.course, s.email, e.event_title, 
                                    r.registration_date, e.event_date, r.registration_status
                             FROM registration r
                             JOIN students s ON r.student_id = s.student_id
                             JOIN events e ON r.event_id = e.event_id
                             WHERE r.registration_status != 'Approved'"; 

        $resultRegistrations = $conn->query($sqlRegistrations);

        if ($resultRegistrations->num_rows > 0) {
            while ($row = $resultRegistrations->fetch_assoc()) {
                echo "<tr>
                        <td>" . ($row['student_number']) . "</td>
                        <td>" . ($row['first_name'] . " " . $row['last_name']) . "</td>
                        <td>" . ($row['course']) . "</td>
                        <td>" . ($row['email']) . "</td>
                        <td>" . ($row['event_title']) . "</td>
                        <td>" . ($row['registration_date']) . "</td>
                        <td>" . ($row['event_date']) . "</td>
                        <td>" . ($row['registration_status']) . "</td>
                        <td>
                            <form method='POST' action='update_registration.php'>
                                <input type='hidden' name='registration_id' value='" . ($row['registration_id']) . "'>
                                <select name='registration_status'>
                                    <option value='Pending'" . ($row['registration_status'] == 'Pending' ? ' selected' : '') . ">Pending</option>
                                    <option value='Approved'" . ($row['registration_status'] == 'Approved' ? ' selected' : '') . ">Approved</option>
                                    <option value='Denied'" . ($row['registration_status'] == 'Denied' ? ' selected' : '') . ">Denied</option>
                                </select>
                                <button type='submit'>Update</button>
                            </form>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No registrations found.</td></tr>";
        }
        ?>
    </tbody>
</table>
</section>


<section class="event-section" id="create_events" style="<?= $currentSection === 'create_events' ? 'display:block;' : 'display:none'; ?>">
    <h2>Create a New Event</h2>
    <form action="create_event.php" method="post" enctype="multipart/form-data">
        <label for="event_title">Event Title:</label>
        <input type="text" id="event_title" name="event_title" required><br><br>

        <label for="event_description">Event Description:</label>
        <textarea id="event_description" name="event_description" required></textarea><br><br>

        <label for="event_date">Event Date:</label>
        <input type="date" id="event_date" name="event_date" min="<?= date('Y-m-d'); ?>" required><br><br>


        <label for="start_time">Start Time:</label>
        <input type="time" id="start_time" name="start_time" required><br><br>

        <label for="end_time">End Time:</label>
        <input type="time" id="end_time" name="end_time" required><br><br>

        <label for="location">Location:</label>
        <input type="text" id="location" name="location" required><br><br>

        <label for="target_audience">Target Audience:</label>
        <select id="target_audience" name="target_audience" required>
            <option value="All Students">All Students</option>
            <option value="Specific Course">Specific Course</option>
        </select><br><br>

        <label for="specific_course">Specific Course: (Optional)</label>
        <select id="specific_course" name="specific_course">
            <option value="">--Select Course--</option>
            <option value="Bachelor of Elementary Education">Bachelor of Elementary Education</option>
            <option value="Bachelor of Secondary Education">Bachelor of Secondary Education</option>
            <option value="Bachelor of Science in Accountancy">Bachelor of Science in Accountancy</option>
            <option value="Bachelor of Science in Business Administration">Bachelor of Science in Business Administration</option>
            <option value="Bachelor of Science in Civil Engineering">Bachelor of Science in Civil Engineering</option>
            <option value="Bachelor of Science in Criminology">Bachelor of Science in Criminology</option>
            <option value="Bachelor of Science in Information Technology">Bachelor of Science in Information Technology</option>
            <option value="Bachelor of Science in Nursing">Bachelor of Science in Nursing</option>
        </select><br><br>

        <label for="registration_limit">Registration Limit:</label>
        <input type="number" id="registration_limit" name="registration_limit" min="0"><br><br>

        <label for="event_type">Event Type:</label>
        <input type="text" id="event_type" name="event_type"><br><br>

        <label for="event_status">Event Status:</label>
        <select id="event_status" name="event_status" required>
            <option value="Upcoming">Upcoming</option>
            <option value="Ongoing">Ongoing</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
        </select><br><br>

        <label for="additional_notes">Additional Notes:</label>
        <textarea id="additional_notes" name="additional_notes"></textarea><br><br>

        <label for="image">Event Image:</label>
        <input type="file" id="image" name="image"><br><br>

        <button type="submit" name="submit">Create Event</button>
    </form>
</section>


<section class="sections" id="manage_events" style="<?= $currentSection === 'manage_events' ? 'display:block;' : 'display:none'; ?>">
    <h2>Manage Events</h2>
    <?php
    $sqlManageEvents = "SELECT event_id, event_title, event_description, event_date, start_time, end_time, location, target_audience, specific_course, registration_limit, event_type, event_status, additional_notes, image_path FROM events ORDER BY event_date ASC";
    $resultManageEvents = $conn->query($sqlManageEvents);
    
    if ($resultManageEvents->num_rows > 0) {
        echo "<table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Location</th>
                        <th>Target Audience</th>
                        <th>Specific Course</th>
                        <th>Registration Limit</th>
                        <th>Event Type</th>
                        <th>Status</th>
                        <th>Additional Notes</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = $resultManageEvents->fetch_assoc()) {
            echo "<tr>
                    <td>" . ($row['event_title']) . "</td>
                    <td>" . ($row['event_description']) . "</td>
                    <td>" . ($row['event_date']) . "</td>
                    <td>" . ($row['start_time']) . "</td>
                    <td>" . ($row['end_time']) . "</td>
                    <td>" . ($row['location']) . "</td>
                    <td>" . ($row['target_audience']) . "</td>
                    <td>" . ($row['specific_course']) . "</td>
                    <td>" . ($row['registration_limit']) . "</td>
                    <td>" . ($row['event_type']) . "</td>
                    <td>" . ($row['event_status']) . "</td>
                    <td>" . ($row['additional_notes']) . "</td>
                    <td><img src='" . ($row['image_path']) . "' alt='" . ($row['event_title']) . "' width='50' height='50'></td>
                    <td>
                        <a href='edit_event.php?id=" . ($row['event_id']) . "'>Edit</a>
                        <a href='delete_event.php?id=" . ($row['event_id']) . "' onclick='return confirm(\"Are you sure you want to delete this event?\");'>Delete</a>
                    </td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No events found.</p>";
    }
    ?>
</section>


<section class="sections" id="manage_accounts" style="<?= $currentSection === 'manage_accounts' ? 'display:block;' : 'display:none;'; ?>">
    <h2>Manage Student Accounts</h2>
    <?php
    $message = "";
    $sqlManageStudents = "SELECT student_id, first_name, last_name, student_number, email, course, section, date_of_birth FROM students ORDER BY last_name ASC";
    $resultManageStudents = $conn->query($sqlManageStudents);
    
    if (isset($_GET['message'])) {
        $message = $_GET['message'];
    }
    if ($resultManageStudents->num_rows > 0) {
        echo "<table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Student Number</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Section</th>
                        <th>Birthday</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = $resultManageStudents->fetch_assoc()) {
            echo "<tr>
                    <td>" . ($row['first_name'] . " " . $row['last_name']) . "</td>
                    <td>" . ($row['student_number']) . "</td>
                    <td>" . ($row['email']) . "</td>
                    <td>" . ($row['course']) . "</td>
                    <td>" . ($row['section']) . "</td>
                    <td>" . (date('F j, Y', strtotime($row['date_of_birth']))) . "</td>
                    <td>
                        <a href='edit_student.php?id=" . ($row['student_id']) . "' class='edit-btn'>Edit</a>
                        <a href='delete_student.php?id=" . ($row['student_id']) . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this account?\");'>Delete</a>
                    </td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No student accounts found.</p>";
    }
    if ($message) {
        echo $message; 
    }
    
    ?>

    <h2>Manage Admin Accounts
        <button onclick="document.getElementById('create-admin').style.display='block'; document.getElementById('manage_accounts').style.display='none';" class="create-btn">
            <span class="material-symbols-outlined">add_circle</span> Add Admin
        </button>
    </h2>

    <?php
    $sqlManageAccounts = "SELECT admin_id, first_name, last_name, email FROM admins ORDER BY last_name ASC";
    $resultManageAccounts = $conn->query($sqlManageAccounts);

    if ($resultManageAccounts->num_rows > 0) {
        echo "<table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = $resultManageAccounts->fetch_assoc()) {
            echo "<tr>
                    <td>" . ($row['first_name'] . " " . $row['last_name']) . "</td>
                    <td>" . ($row['email']) . "</td>
                    <td>
                        <a href='edit_admin.php?id=" . ($row['admin_id']) . "' class='edit-btn'>Edit</a>
                        <a href='delete_admin.php?id=" . ($row['admin_id']) . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this account?\");'>Delete</a>
                    </td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No admin accounts found.</p>";
    }
    ?>
</section>

<section class="sections" id="create-admin" style="display:none;">
    <h2>Create New Admin Account</h2>
    <form action="create_admin.php" method="POST" class="admin-form">
        <div class="form-group">
            <label for="admin_first_name">First Name:</label>
            <input type="text" id="admin_first_name" name="admin_first_name" required placeholder="Enter first name" pattern="[A-Za-z]+" title="Only letters allowed">
        </div>
        <div class="form-group">
            <label for="admin_last_name">Last Name:</label>
            <input type="text" id="admin_last_name" name="admin_last_name" required placeholder="Enter last name" pattern="[A-Za-z]+" title="Only letters allowed">
        </div>
        <div class="form-group">
            <label for="admin_email">Email:</label>
            <input type="email" id="admin_email" name="admin_email" required placeholder="Enter email address">
        </div>
        <div class="form-group">
            <label for="admin_password">Password:</label>
            <input type="password" id="admin_password" name="admin_password" required placeholder="Enter password">
        </div>
        <button type="submit" class="create2-btn">Create Admin</button>
        <button type="button" onclick="document.getElementById('create-admin').style.display='none'; document.getElementById('manage_accounts').style.display='block';" class="cancel-btn">Cancel</button>
    </form>
</section>


    </div>
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
