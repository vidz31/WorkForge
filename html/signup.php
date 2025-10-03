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

        <div class="mb-3 input-group">
          <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
          <select id="role" class="form-select">
            <option value="" disabled selected>Select account type</option>
            <option value="freelancer">Freelancer</option>
            <option value="business">Business Owner</option>
          </select>
        </div>
        <small id="roleError" class="text-danger d-none">Please select your role</small>

        <div class="mb-3 input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" id="name" class="form-control" placeholder="Full Name">
        </div>
        <small id="nameError" class="text-danger d-none">Please enter your name</small>

        <div class="mb-3 input-group">
          <span class="input-group-text"><i class="bi bi-envelope"></i></span>
          <input type="email" id="email" class="form-control" placeholder="Email address">
        </div>
        <small id="emailError" class="text-danger d-none">Please enter a valid email</small>

        <div class="mb-3 input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" id="password" class="form-control" placeholder="Password">
        </div>
        <small id="passwordError" class="text-danger d-none">
          Password must be 8+ chars with uppercase, lowercase, number, and special character
        </small>

        <div class="mb-3 input-group">
          <span class="input-group-text"><i class="bi bi-arrow-repeat"></i></span>
          <input type="password" id="confirmPassword" class="form-control" placeholder="Confirm Password">
        </div>
        <small id="confirmPasswordError" class="text-danger d-none">Passwords do not match</small>
        <button class="btn btn-signup" onclick="validateSignup()">Sign Up</button>

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