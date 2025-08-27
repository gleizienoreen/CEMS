<?php  
session_start();
include 'connect.php';

if (!isset($_SESSION['admin_email'])) {
    header("Location: login-form.php");
    exit;
}

if (isset($_GET['id'])) {
    $eventId = (int)$_GET['id']; 

    $deleteSql = "DELETE FROM events WHERE event_id = $eventId";

    if (mysqli_query($conn, $deleteSql)) {
        $_SESSION['message'] = "<div class='alert success'>Event deleted successfully.</div>";
        header("Location: admin_dashboard.php?section=manage_events&status=success"); 
    } else {
        $_SESSION['message'] = "<div class='alert error'>Failed to delete event.</div>";
        header("Location: admin_dashboard.php?section=manage_events&status=error"); 
    }
} else {
    header("Location: admin_dashboard.php?section=manage_events&status=error"); 
}
exit;
?>
