<?php  
session_start();
include 'connect.php';


if (!isset($_SESSION['admin_email'])) {
    header("Location: login-form.php");
    exit;
}

if (isset($_GET['id'])) {
    $studentId = (int)$_GET['id'];

    $deleteRegistrationsSql = "DELETE FROM registration WHERE student_id = $studentId";

 
    if (mysqli_query($conn, $deleteRegistrationsSql)) {
    
        $deleteStudentSql = "DELETE FROM students WHERE student_id = $studentId";
        
        if (mysqli_query($conn, $deleteStudentSql)) {
            $_SESSION['message'] = "<div class='alert success'>Student account deleted successfully.</div>";
            header("Location: admin_dashboard.php?section=manage_accounts&status=success");
        } else {
            $_SESSION['message'] = "<div class='alert error'>Failed to delete student account.</div>";
            header("Location: admin_dashboard.php?section=manage_accounts&status=error");
        }
    } else {
      
        header("Location: admin_dashboard.php?section=manage_accounts&status=error");
    }
} else {
    header("Location: admin_dashboard.php?section=manage_accounts&status=error");
}
exit;
?>
