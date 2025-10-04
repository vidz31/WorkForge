<?php
require_once '../includes/helpers.php';
require_once '../classes/User.php';

// Redirect if already logged in
Auth::guest();

$error_message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = Utils::sanitize($_POST['email']);
    $password = $_POST['password'];
    $role = Utils::sanitize($_POST['role']);
    
    // Validate inputs
    if(!Validator::required($email) || !Validator::required($password)) {
        $error_message = 'Please fill in all fields';
    } elseif(!Validator::email($email)) {
        $error_message = 'Please enter a valid email address';
    } else {
        $user = new User();
        $user_data = $user->login($email, $password);
        
        if($user_data && $user_data['role'] == $role) {
            Auth::login($user_data);
            Response::redirect('dashboard.php');
        } else {
            $error_message = 'Invalid credentials or role mismatch';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head></head></head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Page</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/Log_in.css">
</head>

<body>
  <div class="main-section">
    <div class="login-wrapper">
      <div class="illustration">
        <img src="../images/Log_in.png" alt="Illustration">
      </div>

      <!-- Login Form Section -->
      <div class="login-box">
        <h2 class="mb-4 fw-bold text-center">Login</h2>
        <?php if($error_message): ?>
        <div class="alert alert-danger" role="alert">
          <?php echo $error_message; ?>
        </div>
        <?php endif; ?>
        <form method="POST" action="">
          <div class="toggle-btns mb-3">
            <button type="button" id="freelancerBtn" class="btn active" onclick="setRole('freelancer')">Freelancer</button>
            <button type="button" id="businessBtn" class="btn" onclick="setRole('business')">Business owner</button>
          </div>
          <input type="hidden" name="role" id="selectedRole" value="freelancer">
          <div class="mb-2">
            <div class="field">
              <i class="bi bi-envelope icon-left"></i>
              <input type="email" name="email" id="email" class="form-control with-icon-left" placeholder="Email address" required>
            </div>
          </div>
          <div class="mb-3">
            <div class="field">
              <i class="bi bi-lock icon-left"></i>
              <input type="password" name="password" id="password" class="form-control with-icon-left" placeholder="Password" required>
            </div>
          </div>
          <button type="submit" class="btn btn-login">Log in</button>
        </form>
        <p class="signup-link mt-3">
          Don't have an account? <a href="signup.php">Sign up</a>
        </p>
      </div>
    </div>
  </div>
  <script>
    function setRole(role) {
      document.getElementById('selectedRole').value = role;
      document.getElementById("freelancerBtn").classList.remove("active");
      document.getElementById("businessBtn").classList.remove("active");
      if (role === "freelancer") {
        document.getElementById("freelancerBtn").classList.add("active");
      } else {
        document.getElementById("businessBtn").classList.add("active");
      }
    }
  </script>
  <script src="../js/main.js"></script>
</body>

</html>