// Navbar include (all pages except index.html)
document.addEventListener("DOMContentLoaded", function () {
  if (
    !window.location.pathname.endsWith('/index.html') &&
    !window.location.pathname.endsWith('\\index.html')
  ) {
    fetch('../html/navbar.html')
      .then(response => response.text())
      .then(data => {
        const navbarDiv = document.getElementById('navbar');
        if (navbarDiv) {
          navbarDiv.innerHTML = data;
        }
      });
  }
});

// Profile image preview (Profile.html)
function setupProfileImageUpload() {
  const fileInput = document.getElementById('fileInput');
  const profileImage = document.getElementById('profileImage');
  if (fileInput && profileImage) {
    fileInput.addEventListener('change', (event) => {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          profileImage.src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }
}

// Proposal form validation (submit_proposal.html)
function setupProposalForm() {
  const submitBtn = document.getElementById("submitProposal");
  if (submitBtn) {
    submitBtn.addEventListener("click", function (e) {
      e.preventDefault();
      let bidAmount = document.getElementById("bidAmount").value.trim();
      let coverMessage = document.getElementById("coverMessage").value.trim();
      let bidError = document.getElementById("bidError");
      let messageError = document.getElementById("messageError");
      let statusBox = document.getElementById("statusBox");
      let isValid = true;
      bidError.textContent = "";
      messageError.textContent = "";
      if (bidAmount === "" || isNaN(bidAmount) || bidAmount <= 0) {
        bidError.textContent = "Please enter a valid bid amount.";
        isValid = false;
      }
      if (coverMessage === "") {
        messageError.textContent = "Please enter your cover message.";
        isValid = false;
      }
      if (isValid) {
        document.getElementById("bidAmount").value = "";
        document.getElementById("coverMessage").value = "";
        statusBox.style.display = "block";
      }
    });
  }
}

// Navbar include (all pages except index.html)
function includeNavbar() {
  const navbarDiv = document.getElementById('navbar');
  if (navbarDiv) {
    fetch('navbar.html')
      .then(res => res.text())
      .then(html => navbarDiv.innerHTML = html);
  }
}

// Login page logic (Log_in.html)
function setupLoginPage() {
  if (document.getElementById("freelancerBtn") && document.getElementById("businessBtn")) {
    let selectedRole = "freelancer";
    window.setRole = function (role) {
      selectedRole = role;
      document.getElementById("freelancerBtn").classList.remove("active");
      document.getElementById("businessBtn").classList.remove("active");
      if (role === "freelancer") {
        document.getElementById("freelancerBtn").classList.add("active");
      } else {
        document.getElementById("businessBtn").classList.add("active");
      }
    };
  }
  if (document.getElementById("email") && document.getElementById("password")) {
    window.validateLogin = function () {
      const email = document.getElementById("email");
      const password = document.getElementById("password");
      const emailError = document.getElementById("emailError");
      const passwordError = document.getElementById("passwordError");
      let isValid = true;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,3}$/;
      if (!emailRegex.test(email.value.trim())) {
        email.classList.add("is-invalid");
        emailError.textContent = "Please enter a valid email (example@domain.com)";
        emailError.classList.remove("d-none");
        isValid = false;
      } else {
        email.classList.remove("is-invalid");
        emailError.classList.add("d-none");
      }
      const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
      if (!passwordRegex.test(password.value.trim())) {
        password.classList.add("is-invalid");
        passwordError.textContent =
          "Password must be 8+ chars with uppercase, lowercase, number, and special character";
        passwordError.classList.remove("d-none");
        isValid = false;
      } else {
        password.classList.remove("is-invalid");
        passwordError.classList.add("d-none");
      }
      if (isValid) {
        window.location.href = "DashBoard.html";
      }
    };
  }
}

// Signup page logic (Sign_up.html)
function setupSignupPage() {
  if (document.getElementById("role") && document.getElementById("name") && document.getElementById("email") && document.getElementById("password") && document.getElementById("confirmPassword")) {
    window.validateSignup = function () {
      const role = document.getElementById("role");
      const name = document.getElementById("name");
      const email = document.getElementById("email");
      const password = document.getElementById("password");
      const confirmPassword = document.getElementById("confirmPassword");
      const roleError = document.getElementById("roleError");
      const nameError = document.getElementById("nameError");
      const emailError = document.getElementById("emailError");
      const passwordError = document.getElementById("passwordError");
      const confirmPasswordError = document.getElementById("confirmPasswordError");
      let isValid = true;
      if (!role.value) {
        role.classList.add("is-invalid");
        roleError.classList.remove("d-none");
        isValid = false;
      } else {
        role.classList.remove("is-invalid");
        roleError.classList.add("d-none");
      }
      if (name.value.trim() === "") {
        name.classList.add("is-invalid");
        nameError.classList.remove("d-none");
        isValid = false;
      } else {
        name.classList.remove("is-invalid");
        nameError.classList.add("d-none");
      }
      const emailRegex = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,3}$/;
      if (!emailRegex.test(email.value.trim())) {
        email.classList.add("is-invalid");
        emailError.textContent = "Please enter a valid email (e.g. user@domain.com)";
        emailError.classList.remove("d-none");
        isValid = false;
      } else {
        email.classList.remove("is-invalid");
        emailError.classList.add("d-none");
      }
      const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
      if (!passwordRegex.test(password.value.trim())) {
        password.classList.add("is-invalid");
        passwordError.classList.remove("d-none");
        isValid = false;
      } else {
        password.classList.remove("is-invalid");
        passwordError.classList.add("d-none");
      }
      if (confirmPassword.value.trim() === "" || confirmPassword.value !== password.value) {
        confirmPassword.classList.add("is-invalid");
        confirmPasswordError.classList.remove("d-none");
        isValid = false;
      } else {
        confirmPassword.classList.remove("is-invalid");
        confirmPasswordError.classList.add("d-none");
      }
      if (isValid) {
        window.location.href = "Log_in.html";
      }
    };
  }
}

// On DOMContentLoaded, initialize relevant scripts for each page
window.addEventListener('DOMContentLoaded', function () {
  includeNavbar();
  setupProfileImageUpload();
  setupProposalForm();
  setupLoginPage();
  setupSignupPage();
});