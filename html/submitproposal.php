<?php
require_once '../includes/helpers.php';
require_once '../classes/Proposal.php';
require_once '../classes/Task.php';
Auth::requireRole('freelancer');
$error_message = '';
$success_message = '';
$task_id = isset($_GET['task_id']) ? (int)$_GET['task_id'] : 0;
if(!$task_id) {
    Response::redirect('browsetask.php');
}
$task_obj = new Task();
$task = $task_obj->getTaskById($task_id);
if(!$task) {
    Response::redirect('browsetask.php');
}
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bid_amount = (float)$_POST['bidAmount'];
    $cover_message = Utils::sanitize($_POST['coverMessage']);
    $freelancer_id = Auth::id();
    if(!Validator::positive($bid_amount)) {
        $error_message = 'Please enter a valid bid amount';
    } elseif(!Validator::required($cover_message)) {
        $error_message = 'Please enter your cover message';
    } else {
        $proposal = new Proposal();
        $result = $proposal->create($task_id, $freelancer_id, $bid_amount, $cover_message);
        if($result['success']) {
            $success_message = 'Proposal submitted successfully!';
        } else {
            $error_message = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Submit Proposal</title>
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/submit_proposal.css">
</head>

<body>
  <!-- Navbar -->
  <div id="navbar"></div>
  <script src="../js/main.js"></script>

  <!-- Submit Proposal Section -->
  <div class="container">
    <h1>Submit a Proposal</h1>
    <h4>Design a Mobile App Landing Page</h4>
    <p class="task-details">Proposals: 3 &nbsp;&nbsp; | &nbsp;&nbsp; Budget: $500 &nbsp;&nbsp; | &nbsp;&nbsp; Deadline:
      May 5, 2024</p>

    <!-- Error/Success Messages -->
    <?php if($error_message): ?>
    <div class="alert alert-danger">
      <?= $error_message ?>
    </div>
    <?php endif; ?>
    <?php if($success_message): ?>
    <div class="alert alert-success">
      <?= $success_message ?>
    </div>
    <?php endif; ?>

    <!-- Proposal Form -->
    <div class="proposal-form">
      <form method="POST">
        <div class="mb-3">
          <label for="bidAmount" class="form-label">Bid Amount</label>
          <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" class="form-control" id="bidAmount" name="bidAmount" placeholder="500" value="<?= isset($bid_amount) ? $bid_amount : '' ?>">
          </div>
        </div>
        <div class="mb-3">
          <label for="coverMessage" class="form-label">Cover Message</label>
          <input type="text" class="form-control" id="coverMessage" name="coverMessage"
            placeholder="Please provide your cover message for the client..." value="<?= isset($cover_message) ? $cover_message : '' ?>">
        </div>
        <button class="btn btn-submit w-100" id="submitProposal" type="submit">Submit Proposal</button>
      </form>
    </div>

    <!-- Proposal Status Box -->
    <div id="statusBox" class="status-box">
      ✅ Proposal Status: Submitted
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="main.js"></script>
</body>

</html>