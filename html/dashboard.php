<?php
require_once '../includes/helpers.php';
require_once '../classes/User.php';
require_once '../classes/Task.php';
Auth::requireAuth();
$user_data = Auth::user();
$user_id = Auth::id();
$role = Auth::role();
$task = new Task();
$stats = $task->getTaskStats($user_id, $role);
$recent_tasks = $task->getTasksByUserId($user_id, $role);
$recent_tasks = array_slice($recent_tasks, 0, 3);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo ucfirst($role); ?> Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../css/DashBoard.css">
</head>

<body>
  <!-- Navbar -->
  <?php include 'navbar.php'; ?>
  <div class="dashboard-wrapper">
    <div class="dashboard-title"><?php echo ucfirst($role); ?> Dashboard</div>
    <div class="dashboard">
      <!-- Stats Card -->
      <div class="card">
        <h3>Your Stats</h3>
        <?php if($role == 'freelancer'): ?>
          <div class="stat">
            <span>Completed Tasks</span>
            <span class="stat-value"><?php echo $user_data['completed_tasks']; ?></span>
          </div>
          <div class="stat">
            <span>Average Rating</span>
            <span class="stat-value"><?php echo number_format($user_data['average_rating'], 1); ?></span>
          </div>
          <div class="stat">
            <span>In Progress</span>
            <span class="stat-value"><?php echo $stats['in_progress'] ?? 0; ?></span>
          </div>
        <?php else: ?>
          <div class="stat">
            <span>Posted Tasks</span>
            <span class="stat-value"><?php echo ($stats['completed'] ?? 0) + ($stats['in_progress'] ?? 0) + ($stats['open'] ?? 0); ?></span>
          </div>
          <div class="stat">
            <span>Completed Tasks</span>
            <span class="stat-value"><?php echo $stats['completed'] ?? 0; ?></span>
          </div>
          <div class="stat">
            <span>Active Tasks</span>
            <span class="stat-value"><?php echo $stats['in_progress'] ?? 0; ?></span>
          </div>
        <?php endif; ?>
      </div>
      <!-- Tasks Card -->
      <div class="card">
        <h3><?php echo $role == 'freelancer' ? 'Your Tasks' : 'Recent Posted Tasks'; ?></h3>
        <?php if(empty($recent_tasks)): ?>
          <p class="text-muted">No tasks found</p>
        <?php else: ?>
          <?php foreach($recent_tasks as $task_item): ?>
            <div class="task">
              <span><?php echo htmlspecialchars($task_item['title']); ?></span>
              <?php if($role == 'freelancer'): ?>
                <span class="status <?php echo strtolower(str_replace('_', '-', $task_item['assignment_status'] ?? $task_item['status'])); ?>">
                  <?php echo ucfirst(str_replace('_', ' ', $task_item['assignment_status'] ?? $task_item['status'])); ?>
                </span>
              <?php else: ?>
                <span class="status <?php echo strtolower(str_replace('_', '-', $task_item['status'])); ?>">
                  <?php echo ucfirst(str_replace('_', ' ', $task_item['status'])); ?>
                </span>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
        <?php if($role == 'business'): ?>
          <div class="mt-3">
            <a href="create_task.php" class="btn btn-primary btn-sm">Post New Task</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>