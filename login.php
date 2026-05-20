<?php
session_start();
include('connection.php');

$user_created_successfully = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // LOGIN with username and password (for Learners)
    if (isset($_POST['logemail']) && isset($_POST['logpass'])) {
        $username = trim($_POST['logemail']);
        $password = trim($_POST['logpass']);

        $stmt = $con->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Verify hashed password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user['username'];
                $_SESSION['fullname']= $user['Fullname'];
                header("Location: index.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "User not found.";
        }

    // SIGNUP for both Learner and Driver
    } elseif (isset($_POST['signup_username'])) {
        $fullname = trim($_POST['logname']);
        $username = trim($_POST['signup_username']);
        $password = trim($_POST['signup_password']);
        $confirm = trim($_POST['signup_confirm_password']);
        $role = $_POST['role'] ?? '';
        $license = $_POST['license_number'] ?? null;

        // Password strength validation
        if (strlen($password) < 8) {
            $error = "Password must be at least 8 characters long.";
        } elseif (!preg_match("#[0-9]+#", $password)) {
            $error = "Password must include at least one number.";
        } elseif (!preg_match("#[a-zA-Z]+#", $password)) {
            $error = "Password must include at least one letter.";
        } elseif (!preg_match("#[^\w]+#", $password)) {
            $error = "Password must include at least one special character.";
        } elseif ($password !== $confirm) {
            $error = "Passwords do not match.";
        } else {
            // If role is Driver, verify license with API
            if ($role === "Driver") {
                $api = file_get_contents("http://rwandalicensehub.atwebpages.com/license.php");
                $data = json_decode($api, true);
                $match = false;

                foreach ($data as $entry) {
                    if (strcasecmp($entry['Fullname'], $fullname) === 0 && $entry['licenceNo'] === $license) {
                        $match = true;
                        break;
                    }
                }

                if (!$match) {
                    $error = "License number does not match the names or not Issued.";
                }
            }

            if (!$error) {
                // Check if username already exists
                $stmt = $con->prepare("SELECT * FROM users WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $res = $stmt->get_result();

                if ($res && $res->num_rows > 0) {
                    $error = "Username or email already exists.";
                } else {
                    // Hash the password before storing
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert user with hashed password
                    $stmt = $con->prepare("INSERT INTO users (Fullname, username, password, role, license_number) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $fullname, $username, $hashed_password, $role, $license);
                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                        $user_created_successfully = true;
                    } else {
                        $error = "Failed to create account.";
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login</title>
  <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.4/components/logins/login-10/assets/css/login-10.css" />
  <link rel="stylesheet" href="stylo.css" />
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="script.js" defer></script>
</head>
<body>
<div class="theme-toggle-container">
  <button id="themeToggle" class="theme-toggle-btn">
    <i class="fa fa-sun-o"></i> Light Mode
  </button>
</div>
<div class="section">
  <div class="container">
    <div class="row full-height justify-content-center">
      <div class="col-12 text-center align-self-center py-5">
        <div class="section pb-5 pt-5 pt-sm-2 text-center">
          <h6 class="mb-0 pb-3"><span>Log In </span><span>Sign Up</span></h6>
          <input class="checkbox" type="checkbox" id="reg-log" name="reg-log" />
          <label for="reg-log"></label>
          
          <div class="card-3d-wrap mx-auto">
            <div class="card-3d-wrapper">

              <!-- Login Card -->
              <div class="card-front">
                <div class="center-wrap">
                  <div class="section text-center">
                    <h4 class="mb-4 pb-3">Log In</h4>

                    <!-- Toggle Between Login Methods -->
                    <div class="mb-3">
                      <button id="toggleUserLogin" class="btn btn-sm btn-outline-light me-2 active" type="button">Use Username</button>
                      <button id="toggleLicenseLogin" class="btn btn-sm btn-outline-light" type="button">Use License No</button>
                    </div>

                    <!-- Username Login -->
                    <div id="userLoginForm">
                      <form action="login.php" method="POST">
                        <div class="form-group">
                          <input type="text" name="logemail" class="form-style" placeholder="Username or email" id="logemail" autocomplete="off" required />
                          <i class="input-icon uil uil-at"></i>
                        </div>
                        <div class="form-group mt-2">
                          <input type="password" name="logpass" class="form-style" placeholder="Password" id="logpass" autocomplete="off" required />
                          <i class="input-icon uil uil-lock-alt"></i>
                        </div>
                        <button type="submit" class="btn mt-4">Login</button>
                      </form
                      <br><br>
                      <a href="#" onclick="event.preventDefault()" class="link mt-3" data-bs-toggle="modal" data-bs-target="#mailModal">Forgot your password?</a>
                      </div>
                    <!-- License Login -->
                    <div id="licenseLoginForm" style="display: none;">
                      <form action="login.php" method="POST">
                        <div class="form-group">
                          <input type="text" name="license_login" class="form-style" placeholder="License Number" id="licenseInput" autocomplete="off" required />
                          <i class="input-icon uil uil-car"></i>
                        </div>
                        <button type="submit" class="btn mt-4">Login</button>
                      </form>
                    </div>

                  </div>
                </div>
              </div>


              <?php

// LOGIN with license number (for Drivers)
if (isset($_POST['license_login'])) {
    $license_number = trim($_POST['license_login']);

    $stmt = $con->prepare("SELECT * FROM users WHERE license_number = ?");
    $stmt->bind_param("s", $license_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user'] = $user['username'];
        $_SESSION['fullname']= $user['Fullname'];
        header("Location: index.php");
        exit;
    } else {
        $error = "License number not found.";
    }
}

?>
              
              <!-- Signup Card -->
              <div class="card-back">
                <div class="center-wrap">
                  <div class="section text-center">
                    <h4 class="mb-4 pb-3">Sign Up</h4>

                    <form action="" method="POST">
                      <div class="form-group">
                        <input type="text" name="logname" class="form-style" placeholder="Full names" id="logname" autocomplete="off" required />
                        <i class="input-icon uil uil-user"></i>
                      </div>

                      <div class="form-group mt-2">
                        <select name="role" id="roleSelect" class="form-style" required>
                          <option value="">--Role--</option>
                          <option value="Driver">Driver</option>
                          <option value="Learner">Learner</option>
                        </select>
                        <i class="input-icon uil uil-user"></i>
                      </div>

                      <!-- Hidden license input (shows when Driver selected) -->
                      <div class="form-group mt-2" id="licenseField" style="display: none;">
                        <input type="text" name="license_number" class="form-style" placeholder="License Number" autocomplete="off" />
                        <i class="input-icon uil uil-car"></i>
                      </div>

                      <div class="form-group mt-2">
                        <input type="text" name="signup_username" class="form-style" placeholder="Username or email" id="signupUsername" autocomplete="off" required />
                        <i class="input-icon uil uil-user"></i>
                      </div>

                     <div class="form-group mt-2">
    <input type="password" name="signup_password" class="form-style" placeholder="Choose Password" id="signupPassword" autocomplete="off" required />
    <i class="input-icon uil uil-lock-alt"></i>
    <div class="password-strength-meter mt-2" style="display: none;">
        <div class="strength-meter-fill" data-strength="0"></div>
    </div>
    <small class="text-muted" style="display: none;" id="password-strength-text-container">
        Password strength: <span id="password-strength-text">Weak</span>
    </small>
</div>
                      <div class="form-group mt-2">
                        <input type="password" name="signup_confirm_password" class="form-style" placeholder="Confirm Password" id="signupConfirmPassword" autocomplete="off" required />
                        <i class="input-icon uil uil-lock-alt"></i>
                      </div>

                      <button type="submit" class="btn mt-4">Create</button>
                    </form>
                  </div>
                </div>
              </div>

            </div>
          </div><!-- card-3d-wrap -->

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="mailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Reset Password Options</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="" method="POST">
          <input type="email" name="email" placeholder="Your email address" class="form-control"/>
        
      </div>
      <div class="modal-footer">
        <input type="submit" name="send" value="Send Mail" class="btn btn-primary col-4"/>
        </form>
      </div>
    </div>
  </div>
</div>
<?php
if (isset($_POST['send'])){
$email=$_POST['email'];
$token = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

include('pass_reset.php');

$sql="UPDATE users SET Token='$token' WHERE username='$email'";
$query=mysqli_query($con,$sql);

if($query){
 echo "<script>
  Swal.fire({
    position: 'top-end',
    icon: 'success',
    title: 'Email Sent',
    text: 'Check your Inbox for Reset Token',
    showConfirmButton: false,
    timer: 3000,
  });
</script>"; 
} else {
 echo "<script>
  Swal.fire({
    position: 'top-end',
    icon: 'error',
    title: 'Email send failed',
    text: 'Mail failed due to an error',
    showConfirmButton: false,
    timer: 3000,
  });
</script>"; 
}
}

?>
<script>
  // Toggle login methods
  document.getElementById('toggleUserLogin').addEventListener('click', function() {
    this.classList.add('active');
    document.getElementById('toggleLicenseLogin').classList.remove('active');
    document.getElementById('userLoginForm').style.display = 'block';
    document.getElementById('licenseLoginForm').style.display = 'none';
  });

  document.getElementById('toggleLicenseLogin').addEventListener('click', function() {
    this.classList.add('active');
    document.getElementById('toggleUserLogin').classList.remove('active');
    document.getElementById('userLoginForm').style.display = 'none';
    document.getElementById('licenseLoginForm').style.display = 'block';
  });

  // Show license field only when role Driver is selected
  document.getElementById('roleSelect').addEventListener('change', function() {
    if (this.value === 'Driver') {
      document.getElementById('licenseField').style.display = 'block';
      document.querySelector('#licenseField input').setAttribute('required', 'required');
    } else {
      document.getElementById('licenseField').style.display = 'none';
      document.querySelector('#licenseField input').removeAttribute('required');
    }
  });

document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('signupPassword');
    const strengthMeter = document.querySelector('.password-strength-meter');
    const strengthFill = document.querySelector('.strength-meter-fill');
    const strengthTextContainer = document.getElementById('password-strength-text-container');
    const strengthText = document.getElementById('password-strength-text');

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            // Show/hide strength meter based on whether there's input
            if (password.length > 0) {
                strengthMeter.style.display = 'block';
                strengthTextContainer.style.display = 'block';
            } else {
                strengthMeter.style.display = 'none';
                strengthTextContainer.style.display = 'none';
                return;
            }
            
            const strength = calculatePasswordStrength(password);
            
            // Update strength meter
            strengthFill.setAttribute('data-strength', strength);
            strengthFill.className = 'strength-meter-fill';
            
            if (strength < 2) {
                strengthFill.classList.add('password-weak');
                strengthText.textContent = 'Weak';
                strengthText.style.color = '#ff4d4d';
            } else if (strength < 4) {
                strengthFill.classList.add('password-medium');
                strengthText.textContent = 'Medium';
                strengthText.style.color = '#ffcc00';
            } else {
                strengthFill.classList.add('password-strong');
                strengthText.textContent = 'Strong';
                strengthText.style.color = '#00cc66';
            }
        });
    }

    function calculatePasswordStrength(password) {
        let strength = 0;
        
        // Length contributes up to 2 points
        if (password.length >= 8) strength += 1;
        if (password.length >= 12) strength += 1;
        
        // Complexity contributes up to 3 points
        if (/[A-Z]/.test(password)) strength += 1; // Uppercase letter
        if (/\d/.test(password)) strength += 1;    // Number
        if (/[^A-Za-z0-9]/.test(password)) strength += 1; // Special char
        
        return strength;
    }
});

document.addEventListener('DOMContentLoaded', function() {
  const themeToggle = document.getElementById('themeToggle');
  const body = document.body;
  
  // Check for saved theme preference or use dark mode as default
  const savedTheme = localStorage.getItem('theme') || 'dark';
  body.classList.toggle('light-mode', savedTheme === 'light');
  updateToggleButton(savedTheme);
  
  themeToggle.addEventListener('click', function() {
    body.classList.toggle('light-mode');
    const currentTheme = body.classList.contains('light-mode') ? 'light' : 'dark';
    localStorage.setItem('theme', currentTheme);
    updateToggleButton(currentTheme);
  });
  
  function updateToggleButton(theme) {
    if (theme === 'light') {
      themeToggle.innerHTML = '<i class="fa fa-moon-o"></i> Dark Mode';
    } else {
      themeToggle.innerHTML = '<i class="fa fa-sun-o"></i> Light Mode';
    }
  }
});

</script>

<?php if ($error): ?>
<script>
  Swal.fire({
    position: 'top-end',
    icon: 'error',
    title: 'Error',
    text: <?php echo json_encode($error); ?>,
    showConfirmButton: false,
    timer: 3000,
   
  });
</script>
<?php elseif ($user_created_successfully): ?>
  <?php include('signupmail.php'); ?>
<script>
  Swal.fire({
    position: 'top-end',
    icon: 'success',
    title: 'Account created',
    text: 'Your account has been created successfully.',
    showConfirmButton: false,
    timer: 3000,
  });
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
