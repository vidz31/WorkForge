<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WorkForage</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../css/index.css">
</head>

<body>

  <!-- Navbar for Landing Page -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container d-flex justify-content-between">
      <a class="navbar-brand fw-bold" href="#">WorkForage</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link login" href="Log_in.html">Log In</a></li>
          <li class="nav-item"><a class="nav-link signup" href="Sign_up.html">Sign Up</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero-section container">
    <div class="row align-items-center">
      <div class="col-lg-6 col-md-12 hero-text text-center text-lg-start">
        <h1>Turn Your Skills<br>Into Opportunities</h1>
      </div>
      <div class="col-lg-6 col-md-12">
        <img src="../images/Landing_page.png" alt="People working illustration" class="img-fluid hero-image">
      </div>
    </div>

    <!-- Floating background icons -->
    <i class="fas fa-cog floating-icon icon-gear"></i>
    <i class="fas fa-pencil-alt floating-icon icon-pen"></i>
    <i class="fas fa-comments floating-icon icon-chat"></i>
    <i class="fas fa-bullhorn floating-icon icon-megaphone"></i>

    <!-- Task Cards -->
    <div class="row task-cards mt-5 g-4">
      <div class="col-md-6 col-lg-3 d-flex">
        <div class="task-card tech-task h-100 w-100">
          <i class="fas fa-cog"></i>
          <h5 class="fw-bold mt-2">Tech Task</h5>
          <p>Implement and maintain software applications</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 d-flex">
        <div class="task-card design-task h-100 w-100">
          <i class="fas fa-pencil-ruler"></i>
          <h5 class="fw-bold mt-2">Design Task</h5>
          <p>Create visual concepts and layouts</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 d-flex">
        <div class="task-card writing-task h-100 w-100">
          <i class="fas fa-file-alt"></i>
          <h5 class="fw-bold mt-2">Writing Task</h5>
          <p>Produce written content and articles</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 d-flex">
        <div class="task-card marketing-task h-100 w-100">
          <i class="fas fa-bullhorn"></i>
          <h5 class="fw-bold mt-2">Marketing Task</h5>
          <p>Promote products or services online</p>
        </div>
      </div>
    </div>

  </section>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>