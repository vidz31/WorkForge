<?php
require_once '../includes/helpers.php';
require_once '../classes/User.php';
require_once '../classes/FreelancerProfile.php';
Auth::requireRole('freelancer');
$user_id = Auth::id();
$success_message = '';
$error_message = '';
if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $upload_result = Utils::uploadFile($_FILES['profile_picture'], '../uploads/profiles/');
    if($upload_result['success']) {
        $user = new User();
        $profile_picture_url = 'uploads/profiles/' . $upload_result['filename'];
        $user->updateProfile($user_id, Auth::user()['full_name'], Auth::user()['email'], $profile_picture_url);
        $success_message = 'Profile picture updated successfully!';
    } else {
        $error_message = $upload_result['message'];
    }
}
if($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_FILES['profile_picture'])) {
    $skills = isset($_POST['skills']) ? $_POST['skills'] : [];
    $description = Utils::sanitize($_POST['description']);
    $hourly_rate = isset($_POST['hourly_rate']) ? (float)$_POST['hourly_rate'] : null;
    $profile = new FreelancerProfile();
    if($profile->createOrUpdate($user_id, $skills, $description, $hourly_rate)) {
        $success_message = 'Profile updated successfully!';
    } else {
        $error_message = 'Failed to update profile';
    }
}
$profile_obj = new FreelancerProfile();
$profile_data = $profile_obj->getProfileByUserId($user_id);
$user_obj = new User();
$user_data = $user_obj->getUserById($user_id);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Freelancer Profile</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/Profile.css">
</head>

<body>
  <!-- Navbar-->
  <div id="navbar"></div>
  <script>
    fetch('navbar.html')
      .then(res => res.text())
      .then(html => document.getElementById('navbar').innerHTML = html);
  </script>

  <!-- Profile Section -->
  <div class="container">
    <h1 class="text-white fw-bold mt-4">Freelancer Profile</h1>
    <p class="text-white">Add/edit skills, description, profile picture</p>
    <?php if($success_message): ?>
    <div class="alert alert-success" role="alert">
      <?php echo $success_message; ?>
    </div>
    <?php endif; ?>
    <?php if($error_message): ?>
    <div class="alert alert-danger" role="alert">
      <?php echo $error_message; ?>
    </div>
    <?php endif; ?>
    <div class="profile-card">
      <div class="d-flex align-items-center gap-3">
        <img id="profileImage" src="<?php echo $user_data['profile_picture'] ? '../' . $user_data['profile_picture'] : 'https://cdn-icons-png.flaticon.com/512/847/847969.png'; ?>" alt="Profile Picture" class="profile-img">
        <form method="POST" enctype="multipart/form-data" style="display: inline;">
          <input type="file" id="fileInput" name="profile_picture" accept="image/*" hidden onchange="this.form.submit()">
          <button type="button" class="edit-btn" onclick="document.getElementById('fileInput').click()">Edit Picture</button>
        </form>
      </div>
      <form method="POST" action="">
        <div class="mt-4">
          <h3 class="section-title">Skills</h3>
          <div class="mb-3">
            <label class="form-label">Select your skills:</label>
            <div class="row">
              <?php 
              $available_skills = [
                'Responsive web design', 'User interface (UI)', 'Python programming', 
                'JavaScript', 'Mobile App Development', 'Logo Design', 'Graphic Design',
                'Web Security', 'Blog Writing', 'Social Media Marketing', 'Digital Marketing'
              ];
              $current_skills = isset($profile_data['skills_array']) ? $profile_data['skills_array'] : [];
              foreach($available_skills as $skill): ?>
                <div class="col-md-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="skills[]" value="<?php echo $skill; ?>" <?php echo in_array($skill, $current_skills) ? 'checked' : ''; ?>>
                    <label class="form-check-label"><?php echo $skill; ?></label>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <div class="mt-3">
          <h3 class="section-title">Description</h3>
          <textarea name="description" class="form-control" rows="4" placeholder="Tell clients about yourself..."><?php echo isset($profile_data['description']) ? htmlspecialchars($profile_data['description']) : ''; ?></textarea>
        </div>
        <div class="mt-3">
          <h3 class="section-title">Hourly Rate (Optional)</h3>
          <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" name="hourly_rate" class="form-control" step="0.01" value="<?php echo isset($profile_data['hourly_rate']) ? $profile_data['hourly_rate'] : ''; ?>" placeholder="25.00">
            <span class="input-group-text">per hour</span>
          </div>
        </div>
        <div class="mt-4">
          <button type="submit" class="btn btn-primary">Update Profile</button>
        </div>
      </form>
      <div class="footer-stats d-flex justify-content-between mt-4">
        <span>Average Rating: <?php echo number_format($user_data['average_rating'], 1); ?></span>
        <span>Completed Tasks: <?php echo $user_data['completed_tasks']; ?></span>
      </div>
    </div>
  </div>

  <!-- Bootstrap + JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../js/main.js"></script>
</body>

</html>