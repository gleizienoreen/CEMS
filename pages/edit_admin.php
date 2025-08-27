<?php  
session_start();
include 'connect.php';

if (!isset($_SESSION['admin_email'])) {
    header("Location: login-form.php");
    exit;
}

$message = "";


if (isset($_GET['id'])) {
    $adminId = (int)$_GET['id'];
    $sql = "SELECT first_name, last_name, email FROM admins WHERE admin_id = $adminId";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $admin = mysqli_fetch_assoc($result);
    } else {
        echo "Admin not found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];

    
    $updateSql = "UPDATE admins SET first_name = '$firstName', last_name = '$lastName', email = '$email' WHERE admin_id = $adminId";


    error_log($updateSql); 

    if (mysqli_query($conn, $updateSql)) {

        if (mysqli_affected_rows($conn) > 0) {
            $message = "<p class='success-message'>Admin details updated successfully.</p>";
        } else {
            $message = "<p style='color: red;'>No changes were made. Please verify the data.</p>";
        }
    } else {
        $message = "<p style='color: red;'>Error updating admin details: " . mysqli_error($conn) . "</p>";
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin</title>
    <link rel="stylesheet" href="admin.css"> 
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #212c61;
            text-align: center;
            margin-bottom: 10px;
        }
        form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: auto;
        }
        label {
            display: block;
            margin-bottom: 15px;
            margin-top: 5px;
            color: #212c61;
        }
        input[type="text"], input[type="email"] {
            width: calc(100% - 10px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #212c61;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #333;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            text-decoration: none;
            color: #212c61;
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
    </style>
</head>
<body>
    <h2>Edit Admin Details</h2>
    <form action="edit_admin.php?id=<?= $adminId; ?>" method="POST">
        <?= $message; ?> 
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?= ($admin['first_name']); ?>" required>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?= ($admin['last_name']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= ($admin['email']); ?>" required>

        <button type="submit">Update Admin</button>
    </form>
    <a href="admin_dashboard.php?section=manage_accounts">Back to Manage Accounts</a>
</body>
</html>
