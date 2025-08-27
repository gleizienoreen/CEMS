<?php   
session_start(); 
include 'connect.php'; 

if (isset($_SESSION['student_email']) && isset($_POST['event_id'])) {

    $student_email = $_SESSION['student_email'];
    

    $student_query = "SELECT student_id, course FROM students WHERE email = '$student_email'";
    $student_result = mysqli_query($conn, $student_query);
    
    if ($student_result && mysqli_num_rows($student_result) > 0) {
        $student_row = mysqli_fetch_assoc($student_result);
        $student_id = $student_row['student_id'];
        $student_course = $student_row['course'];
        $event_id = $_POST['event_id'];


        $check_registration = "SELECT * FROM registration WHERE student_id = '$student_id' AND event_id = '$event_id'";
        $registration_result = mysqli_query($conn, $check_registration);

        if (mysqli_num_rows($registration_result) > 0) {
            $_SESSION['message'] = "<div class='alert error'>Error: You have already registered for this event.</div>";
        } else {
 
            $sql = "INSERT INTO registration (student_id, event_id, registration_date, registration_status)
                    VALUES ('$student_id', '$event_id', NOW(), 'pending')";

            if (mysqli_query($conn, $sql)) {
                $_SESSION['message'] = "<div class='alert success'>Registration successful! Your request has been sent to the admin for approval.</div>";
            } else {
                $_SESSION['message'] = "<div class='alert error'>Error: " . mysqli_error($conn) . "</div>"; 
            }
        }
    } else {
        $_SESSION['message'] = "<div class='alert error'>Error: Student not found.</div>";
    }
} else {
    $_SESSION['message'] = "<div class='alert error'>Error: Missing student or event information.</div>";
}

header("Location: http://localhost:3000/user.php"); 
exit();
?>
