<?php
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle task deletion
if (isset($_POST['delete_task'])) {
    $task_id = $_POST['task_id'];
    $sql = "DELETE FROM tasks WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $task_id, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
}

// Handle task status update
if (isset($_POST['update_status'])) {
    $task_id = $_POST['task_id'];
    $new_status = $_POST['new_status'];
    $sql = "UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $new_status, $task_id, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
}

// Fetch all tasks for the current user
$sql = "SELECT * FROM tasks WHERE user_id = ? ORDER BY 
    CASE priority 
        WHEN 'High' THEN 1 
        WHEN 'Medium' THEN 2 
        WHEN 'Low' THEN 3 
    END, 
    due_date ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List with Priority</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Todo List</h1>
            <div class="user-info">
                Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
        
        <!-- Add Task Form -->
        <div class="add-task-form">
            <h2>Add New Task</h2>
            <form action="add_task.php" method="POST">
                <div class="form-group">
                    <input type="text" name="title" placeholder="Task Title" required>
                </div>
                <div class="form-group">
                    <textarea name="description" placeholder="Task Description"></textarea>
                </div>
                <div class="form-group">
                    <input type="date" name="due_date" required>
                </div>
                <div class="form-group">
                    <input type="text" name="task_time" placeholder="Enter time (e.g., 2:30 PM, 14:30)" required>
                </div>
                <div class="form-group">
                    <select name="priority" required>
                        <option value="">Select Priority</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="text" name="category" placeholder="Category (optional)">
                </div>
                <button type="submit" class="btn-add">Add Task</button>
            </form>
        </div>

        <!-- Task Filters -->
        <div class="filters">
            <select id="priorityFilter" onchange="filterTasks()">
                <option value="all">All Priorities</option>
                <option value="High">High Priority</option>
                <option value="Medium">Medium Priority</option>
                <option value="Low">Low Priority</option>
            </select>
            <select id="statusFilter" onchange="filterTasks()">
                <option value="all">All Status</option>
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
            </select>
        </div>

        <!-- Task List -->
        <div class="task-list">
            <?php while ($task = mysqli_fetch_assoc($result)): ?>
                <div class="task-card" data-priority="<?php echo $task['priority']; ?>" data-status="<?php echo $task['status']; ?>">
                    <div class="task-content">
                        <div class="task-header">
                            <h3><?php echo htmlspecialchars($task['title']); ?></h3>
                            <span class="priority-badge <?php echo strtolower($task['priority']); ?>">
                                <?php echo htmlspecialchars($task['priority']); ?>
                            </span>
                        </div>
                        <p class="description"><?php echo htmlspecialchars($task['description']); ?></p>
                        <div class="task-details">
                            <span><i class="far fa-calendar"></i> <?php echo date('M d, Y', strtotime($task['due_date'])); ?></span>
                            <span><i class="far fa-clock"></i> <?php echo date('h:i A', strtotime($task['task_time'])); ?></span>
                        </div>
                        <div class="task-actions">
                            <form action="update_status.php" method="POST" style="flex: 1;">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" class="btn-status">
                                    <i class="fas fa-check"></i> Mark as <?php echo $task['status'] === 'completed' ? 'Incomplete' : 'Complete'; ?>
                                </button>
                            </form>
                            <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="delete_task.php" method="POST" style="flex: 1;" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" class="btn-delete">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html> 