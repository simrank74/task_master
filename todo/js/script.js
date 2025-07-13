// Function to filter tasks based on priority and status
function filterTasks() {
    const priorityFilter = document.getElementById('priorityFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const tasks = document.querySelectorAll('.task-card');

    tasks.forEach(task => {
        const taskPriority = task.dataset.priority;
        const taskStatus = task.dataset.status;
        
        const priorityMatch = priorityFilter === 'all' || taskPriority === priorityFilter;
        const statusMatch = statusFilter === 'all' || taskStatus === statusFilter;

        task.style.display = priorityMatch && statusMatch ? 'block' : 'none';
    });
}

// Function to sort tasks
function sortTasks(sortBy) {
    const taskList = document.querySelector('.task-list');
    const tasks = Array.from(document.querySelectorAll('.task-card'));

    tasks.sort((a, b) => {
        if (sortBy === 'priority') {
            const priorityOrder = { 'High': 1, 'Medium': 2, 'Low': 3 };
            return priorityOrder[a.dataset.priority] - priorityOrder[b.dataset.priority];
        } else if (sortBy === 'date') {
            const dateA = new Date(a.querySelector('.due-date').textContent.split(': ')[1]);
            const dateB = new Date(b.querySelector('.due-date').textContent.split(': ')[1]);
            return dateA - dateB;
        }
    });

    // Clear and reappend sorted tasks
    taskList.innerHTML = '';
    tasks.forEach(task => taskList.appendChild(task));
}

// Add event listeners when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Add sorting buttons if they don't exist
    const filters = document.querySelector('.filters');
    if (!document.querySelector('.sort-buttons')) {
        const sortButtons = document.createElement('div');
        sortButtons.className = 'sort-buttons';
        sortButtons.innerHTML = `
            <button onclick="sortTasks('priority')">Sort by Priority</button>
            <button onclick="sortTasks('date')">Sort by Date</button>
        `;
        filters.appendChild(sortButtons);
    }
}); 