<?php   
session_start();
include 'connect.php';

if (!isset($_SESSION['admin_email'])) {
    header("Location: login-form.php");
    exit;
}


if (isset($_GET['admin_id'])) {
    $adminId = (int)$_GET['admin_id'];

    $deleteAdminSql = "DELETE FROM admins WHERE admin_id = $adminId";
    
    if (mysqli_query($conn, $deleteAdminSql)) {
        $_SESSION['message'] = "<div class='alert success'>Admin account deleted.</div>";
        header("Location: admin_dashboard.php?section=manage_accounts&status=success");
    } else {
        $_SESSION['message'] = "<div class='alert error'>Failed to delete admin account.</div>";
        header("Location: admin_dashboard.php?section=manage_accounts&status=error");
    }
} else {
    header("Location: admin_dashboard.php?section=manage_accounts&status=error");
}
exit;
?>
