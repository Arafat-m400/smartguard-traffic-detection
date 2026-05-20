<?php
session_start();
include('connection.php');

$success = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $token = trim($_POST['token']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Password strength validation
    if (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif (!preg_match("#[0-9]+#", $new_password)) {
        $error = "Password must include at least one number.";
    } elseif (!preg_match("#[a-zA-Z]+#", $new_password)) {
        $error = "Password must include at least one letter.";
    } elseif (!preg_match("#[^\w]+#", $new_password)) {
        $error = "Password must include at least one special character.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $con->prepare("SELECT * FROM users WHERE username = ? AND Token = ?");
        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password and clear token
            $update = $con->prepare("UPDATE users SET password = ?, Token = NULL WHERE username = ?");
            $update->bind_param("ss", $hashed_password, $email);
            $update->execute();

            if ($update->affected_rows > 0) {
                $success = true;
            } else {
                $error = "Failed to reset password.";
            }
        } else {
            $error = "Invalid token or email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Password Reset</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background: #f5f7fa;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .strength-meter {
            height: 5px;
        }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card p-4 w-100" style="max-width: 500px;">
        <h3 class="text-center mb-4">Reset Your Password</h3>

       <?php if ($success): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Password Reset Successful',
            text: 'You can now log in with your new password.',
            confirmButtonText: 'Go to Login',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'login.php';
            }
        });
    </script>
<?php elseif ($error): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: <?= json_encode($error) ?>,
            showConfirmButton: true,
        });
    </script>
<?php endif; ?>


        <form method="POST" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Your Email</label>
                <input type="email" name="email" required class="form-control" placeholder="Enter your email">
            </div>

            <div class="mb-3">
                <label for="token" class="form-label">Token</label>
                <input type="text" name="token" required class="form-control" placeholder="Enter your token">
            </div>

            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" id="new_password" name="new_password" required class="form-control" placeholder="New password" oninput="checkStrength()">
                <div class="strength-meter mt-2">
                    <div id="strengthBar" class="w-100 bg-secondary"></div>
                </div>
                <small id="strengthText" class="text-muted"></small>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" required class="form-control" placeholder="Confirm password">
            </div>

            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
        </form>
    </div>
</div>

<script>
function checkStrength() {
    const password = document.getElementById("new_password").value;
    const strengthBar = document.getElementById("strengthBar");
    const strengthText = document.getElementById("strengthText");

    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[A-Z]/i.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^\w]/.test(password)) strength++;

    switch(strength) {
        case 0:
        case 1:
            strengthBar.style.width = "25%";
            strengthBar.className = "bg-danger strength-meter";
            strengthText.innerText = "Weak";
            break;
        case 2:
            strengthBar.style.width = "50%";
            strengthBar.className = "bg-warning strength-meter";
            strengthText.innerText = "Moderate";
            break;
        case 3:
            strengthBar.style.width = "75%";
            strengthBar.className = "bg-info strength-meter";
            strengthText.innerText = "Good";
            break;
        case 4:
            strengthBar.style.width = "100%";
            strengthBar.className = "bg-success strength-meter";
            strengthText.innerText = "Strong";
            break;
    }
}
</script>
</body>
</html>
