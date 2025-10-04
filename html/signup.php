<?php
require_once '../includes/helpers.php';
require_once '../classes/User.php';
require_once '../classes/FreelancerProfile.php';
Auth::guest();
$error_message = '';
$success_message = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = Utils::sanitize($_POST['role']);
    $full_name = Utils::sanitize($_POST['name']);
    $email = Utils::sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmPassword'];
    if(!Validator::required($role) || !Validator::required($full_name) || !Validator::required($email) || !Validator::required($password) || !Validator::required($confirm_password)) {
        $error_message = 'Please fill in all fields';
    } elseif(!Validator::email($email)) {
        $error_message = 'Please enter a valid email address';
    } elseif(!Validator::password($password)) {
        $error_message = 'Password must be 8+ chars with uppercase, lowercase, number, and special character';
    } elseif($password !== $confirm_password) {
        $error_message = 'Passwords do not match';
    } else {
        $user = new User();
        if($user->emailExists($email)) {
            $error_message = 'Email already exists';
        } else {
            $user_id = $user->create($full_name, $email, $password, $role);
            if($user_id) {
                if($role == 'freelancer') {
                    $profile = new FreelancerProfile();
                    $profile->createOrUpdate($user_id, '[]', 'Please add your description...');
                }
                $success_message = 'Account created successfully! Please log in.';
            } else {
                $error_message = 'Failed to create account. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up Page</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/Sign_up.css">
</head>

<body>

  <div class="main-section">
    <div class="signup-wrapper">

      <!-- Sign Up Form Section -->
      <div class="signup-box">
        <h2 class="mb-4 fw-bold text-center">Sign Up</h2>
        <?php if($error_message): ?>
        <div class="alert alert-danger" role="alert">
          <?php echo $error_message; ?>
        </div>
        <?php endif; ?>
        <?php if($success_message): ?>
        <div class="alert alert-success" role="alert">
          <?php echo $success_message; ?>
        </div>
        <?php endif; ?>
        <form method="POST" action="">
          <div class="mb-3 input-group">
            <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
            <select name="role" id="role" class="form-select" required>
              <option value="" disabled selected>Select account type</option>
              <option value="freelancer">Freelancer</option>
              <option value="business">Business Owner</option>
            </select>
          </div>
          <div class="mb-3 input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="name" id="name" class="form-control" placeholder="Full Name" required>
          </div>
          <div class="mb-3 input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" id="email" class="form-control" placeholder="Email address" required>
          </div>
          <div class="mb-3 input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
          </div>
          <div class="mb-3 input-group">
            <span class="input-group-text"><i class="bi bi-arrow-repeat"></i></span>
            <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" placeholder="Confirm Password" required>
          </div>
          <button type="submit" class="btn btn-signup">Sign Up</button>
        </form>
        <p class="login-link mt-3">
          Already have an account? <a href="login.php">Log in</a>
        </p>
      </div>

      <div class="illustration">
        <img src="../images/Sign_up.png" alt="Illustration">
      </div>

    </div>
  </div>
  <script src="../js/main.js"></script>
</body>

</html>