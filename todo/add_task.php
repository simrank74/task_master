<?php
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $task_time = trim($_POST['task_time']);
    $priority = $_POST['priority'];
    $category = trim($_POST['category']);
    
    // Basic validation
    if (empty($title) || empty($due_date) || empty($task_time) || empty($priority)) {
        die("Required fields cannot be empty");
    }

    // Convert time to 24-hour format
    $time_obj = DateTime::createFromFormat('g:i A', $task_time);
    if (!$time_obj) {
        $time_obj = DateTime::createFromFormat('H:i', $task_time);
    }
    if (!$time_obj) {
        die("Invalid time format. Please use format like '2:30 PM' or '14:30'");
    }
    $task_time = $time_obj->format('H:i:s');

    // Prepare and execute the SQL statement
    $sql = "INSERT INTO tasks (user_id, title, description, due_date, task_time, priority, category, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";
    
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        die("Error preparing statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "issssss", $_SESSION['user_id'], $title, $description, $due_date, $task_time, $priority, $category);
    
    if (mysqli_stmt_execute($stmt)) {
        // Redirect back to the main page
        header("Location: dashboard.php");
        exit();
    } else {
        die("Error adding task: " . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);
} else {
    // If not a POST request, redirect to the main page
    header("Location: dashboard.php");
    exit();
}
?> 