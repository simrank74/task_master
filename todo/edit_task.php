<?php
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];
    $category = trim($_POST['category']);
    $status = $_POST['status'];
    
    // Basic validation
    if (empty($title) || empty($due_date) || empty($priority)) {
        die("Required fields cannot be empty");
    }

    // Update the task
    $sql = "UPDATE tasks SET 
            title = ?, 
            description = ?, 
            due_date = ?, 
            priority = ?, 
            category = ?,
            status = ?
            WHERE id = ? AND user_id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssii", $title, $description, $due_date, $priority, $category, $status, $task_id, $_SESSION['user_id']);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: dashboard.php");
        exit();
    } else {
        die("Error updating task: " . mysqli_error($conn));
    }
}

// Get task data for editing
if (isset($_GET['id'])) {
    $task_id = $_GET['id'];
    $sql = "SELECT * FROM tasks WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $task_id, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $task = mysqli_fetch_assoc($result);

    if (!$task) {
        die("Task not found or you don't have permission to edit it");
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Edit Task</h1>
            <div class="user-info">
                Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
        
        <div class="add-task-form">
            <form action="edit_task.php" method="POST">
                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                
                <div class="form-group">
                    <input type="text" name="title" placeholder="Task Title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <textarea name="description" placeholder="Task Description"><?php echo htmlspecialchars($task['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <input type="date" name="due_date" value="<?php echo $task['due_date']; ?>" required>
                </div>
                
                <div class="form-group">
                    <select name="priority" required>
                        <option value="High" <?php echo $task['priority'] === 'High' ? 'selected' : ''; ?>>High</option>
                        <option value="Medium" <?php echo $task['priority'] === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="Low" <?php echo $task['priority'] === 'Low' ? 'selected' : ''; ?>>Low</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <input type="text" name="category" placeholder="Category (optional)" value="<?php echo htmlspecialchars($task['category']); ?>">
                </div>
                
                <div class="form-group">
                    <select name="status">
                        <option value="Pending" <?php echo $task['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Completed" <?php echo $task['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-add">Update Task</button>
                    <a href="dashboard.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 