<?php
require_once '../includes/helpers.php';
require_once '../classes/Task.php';

// Require business owner authentication
Auth::requireRole('business');

$error_message = '';
$success_message = '';

// Handle task creation
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = Utils::sanitize($_POST['title']);
    $description = Utils::sanitize($_POST['description']);
    $category = Utils::sanitize($_POST['category']);
    $budget = (float)$_POST['budget'];
    $deadline = Utils::sanitize($_POST['deadline']);
    $client_id = Auth::id();
    
    // Validate inputs
    if(!Validator::required($title) || !Validator::required($description) || 
       !Validator::required($category) || !Validator::positive($budget) || 
       !Validator::required($deadline)) {
        $error_message = 'Please fill in all fields correctly';
    } elseif(!Validator::date($deadline)) {
        $error_message = 'Please enter a valid deadline date';
    } elseif(strtotime($deadline) <= time()) {
        $error_message = 'Deadline must be in the future';
    } else {
        $task = new Task();
        $task_id = $task->create($client_id, $title, $description, $category, $budget, $deadline);
        
        if($task_id) {
            $success_message = 'Task posted successfully!';
        } else {
            $error_message = 'Failed to create task. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Task</title>
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/Browse_task.css">
</head>

<body>
  <!-- Navbar -->
  <?php include 'navbar_new.php'; ?>

  <!-- Create Task Section -->
  <div class="container">
    <h1>Post a New Task</h1>

    <?php if($error_message): ?>
    <div class="alert alert-danger" role="alert">
      <?php echo $error_message; ?>
    </div>
    <?php endif; ?>

    <?php if($success_message): ?>
    <div class="alert alert-success" role="alert">
      <?php echo $success_message; ?>
      <div class="mt-2">
        <a href="dashboard_new.php" class="btn btn-primary btn-sm">Go to Dashboard</a>
        <a href="browse_tasks_new.php" class="btn btn-secondary btn-sm">View All Tasks</a>
      </div>
    </div>
    <?php endif; ?>

    <?php if(!$success_message): ?>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-body">
            <form method="POST" action="">
              <div class="mb-3">
                <label for="title" class="form-label">Task Title</label>
                <input type="text" name="title" id="title" class="form-control" 
                       placeholder="e.g., Develop a Mobile App" required>
              </div>

              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="5" 
                          placeholder="Provide detailed description of the task..." required></textarea>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="category" class="form-label">Category</label>
                  <select name="category" id="category" class="form-select" required>
                    <option value="">Select Category</option>
                    <option value="Tech">Tech</option>
                    <option value="Design">Design</option>
                    <option value="Writing">Writing</option>
                    <option value="Marketing">Marketing</option>
                  </select>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="budget" class="form-label">Budget</label>
                  <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="budget" id="budget" class="form-control" 
                           step="0.01" min="5" placeholder="500.00" required>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label for="deadline" class="form-label">Deadline</label>
                <input type="date" name="deadline" id="deadline" class="form-control" 
                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Post Task</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>