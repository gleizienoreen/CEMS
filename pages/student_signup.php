<?php 
session_start();
include 'connect.php';

$error = '';

$courses = [
    'Bachelor of Elementary Education',
    'Bachelor of Secondary Education',
    'Bachelor of Science in Accountancy',
    'Bachelor of Science in Business Administration',
    'Bachelor of Science in Civil Engineering',
    'Bachelor of Science in Criminology',
    'Bachelor of Science in Information Technology',
    'Bachelor of Science in Nursing'
];

$courseResult = $conn->query("SELECT DISTINCT course FROM students");
if ($courseResult) {
    while ($row = $courseResult->fetch_assoc()) {
        $courseFromDB = $row['course'];
        if (!in_array($courseFromDB, $courses) && !empty($courseFromDB)) {
            $courses[] = $courseFromDB;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signUp'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $student_number = $_POST['student_number'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $date_of_birth = $_POST['date_of_birth'];
    $password = $_POST['password'];


    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $checkEmailQuery = "SELECT * FROM students WHERE email = '$email'";
    $emailResult = $conn->query($checkEmailQuery);

    if ($emailResult && $emailResult->num_rows > 0) {
        $error = "Email already exists. Please use a different email.";
    } else {
        
        $sql = "INSERT INTO students (first_name, last_name, student_number, email, course, date_of_birth, password)
                VALUES ('$first_name', '$last_name', '$student_number', '$email', '$course','$date_of_birth', '$hashedPassword')";

        if ($conn->query($sql) === TRUE) {
            header("Location: user.php"); 
            exit();
        } else {
            echo "Error: " . $conn->error; 
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <style>
         * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            background-image: linear-gradient(
                rgba(42, 49, 113, 0.6),
                rgba(27, 27, 27, 0.6)
            ), url('images/bg-home.png');
        }

        .signup-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #ffffff;
            border-radius: 15px;
            padding: 40px 30px;
            width: 90%;
            max-width: 450px; 
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        .logo {
            max-width: 100px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 30px;
            color: #212c61;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .input-field,
        .select-field {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            color: #212c61;
            transition: all 0.3s ease;
        }

        .input-field:focus,
        .select-field:focus {
            border-color: #ffd000;
            box-shadow: 0 0 8px rgba(255, 208, 0, 0.5);
            outline: none;
        }

        .btn-signup {
            width: 100%;
            padding: 12px 0;
            font-size: 18px;
            font-weight: bold;
            background-color: #ffd000;
            color: #212c61;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-signup:hover {
            background-color: #212c61;
            color: #f7f7f7;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .login-redirect {
            font-size: 14px;
            color: #212c61;
            margin-top: 15px;
            text-align: center;
        }

        .login-redirect a {
            color: #ffd000;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .login-redirect a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2 class="title">Sign Up</h2>
        <?php if (!empty($error)): ?>
            <div style="color: red;"><?php echo ($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="student_signup.php">
            <input type="text" class="input-field" id="first_name" name="first_name" placeholder="First Name" required>
            <input type="text" class="input-field" id="last_name" name="last_name" placeholder="Last Name" required>
            <input type="text" class="input-field" id="student_number" name="student_number" placeholder="Student Number (e.g. 01-0000-000000)" required>
            <input type="email" class="input-field" id="email" name="email" placeholder="Email" required>
            <select id="course" name="course" class="select-field" required>
                <option value="">Select Course</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo ($course); ?>"><?php echo ($course); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" class="input-field" id="date_of_birth" name="date_of_birth" required>
            <input type="password" class="input-field" id="password" name="password" placeholder="Password" required>
            <button type="submit" name="signUp" class="btn-signup">Sign Up</button>
        </form>
        <div class="login-redirect">
            Already have an account? <a href="login-form.php">Log in</a>
        </div>
    </div>
</body>
</html>
