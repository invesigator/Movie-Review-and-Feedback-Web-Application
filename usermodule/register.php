<?php
session_start();

// Show the success message if registration was successful
if (isset($_SESSION['registration_success']) && $_SESSION['registration_success']) {
    echo "<script>alert('Profile created successfully! Please log in to continue.');</script>";
    
    // Unset the success flag
    unset($_SESSION['registration_success']);

    // Redirect to the login page after the alert
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}

// Logged in users are redirected to the home page
if (isset($_SESSION['email'])) {
    header("location: ../moviemodule/movie.php");
    exit;
}

$first_name = "";
$last_name = "";
$email = "";
$phone = "";
$address = "";

$first_name_error = "";
$last_name_error = "";
$email_error = "";
$phone_error = "";
$address_error = "";
$password_error = "";
$confirm_password_error = "";
$profile_pic_error = "";

$error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // validate first name
    if (empty($first_name)) {
        $first_name_error = "First name is required";
        $error = true;
    }

    // validate last name
    if (empty($last_name)) {
        $last_name_error = "Last name is required";
        $error = true;
    }

    // validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Email is not valid";
        $error = true;
    }

    include "../components/database.php";
    $dbConnection = getDatabaseConnection();

    $statement = $dbConnection->prepare("SELECT id FROM users WHERE email = ?");

    // Bind variables to the prepared statement as parameter ("s" means string)
    $statement->bind_param("s", $email);

    // Execute statement
    $statement->execute();

    // Check if email is already in the database
    $statement->store_result();
    if ($statement->num_rows() > 0) {
        $email_error = "Email is already used";
        $error = true;
    }

    // Close this statement otherwise we cannot prepare another statement
    $statement->close();

    // validate phone number
    if (!preg_match("/^(\\+?60|0)1[0-9]{1}[-]?[0-9]{7}$/", $phone)) {
        $phone_error = "Phone format is not valid";
        $error = true;
    }

    // validate password
    if (strlen($password) < 6) {
        $password_error = "Password must have at least 6 characters";
        $error = true;
    }

    // validate confirm password
    if ($confirm_password != $password) {
        $confirm_password_error = "Password and Confirm Password do not match";
        $error = true;
    }

    // Handle file upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed_ext = array("jpg", "jpeg", "png", "gif");
        $file_name = $_FILES['profile_pic']['name'];
        $file_size = $_FILES['profile_pic']['size'];
        $file_tmp = $_FILES['profile_pic']['tmp_name'];
        $file_ext = strtolower(end(explode('.', $file_name)));

        if (in_array($file_ext, $allowed_ext) === false) {
            $profile_pic_error = "Extension not allowed, please choose a JPG, JPEG, PNG, or GIF file.";
            $error = true;
        }

        if ($file_size > 5097152) {
            $profile_pic_error = "File size must be less than 5 MB";
            $error = true;
        }

        if (!$error) {
            $profile_pic = "../uploads/" . uniqid() . '.' . $file_ext;
            move_uploaded_file($file_tmp, $profile_pic);
        }
    } else {
        $profile_pic = "../uploads/default-avatar.png"; // Set default profile picture
    }

    if (!$error) {
        // All fields are valid: create a new user
        $password = password_hash($password, PASSWORD_DEFAULT);
        $create_at = date("Y-m-d H:i:s");

        // Use prepared statements to avoid SQL injection
        $statement = $dbConnection->prepare(
            "INSERT INTO users (first_name, last_name, email, phone, address, password, create_at, profile_pic)" .
            "VALUES(?, ?, ?, ?, ?, ?, ?, ?)"
        );

        // Bind variables to the prepared statement as parameters
        $statement->bind_param('ssssssss', $first_name, $last_name, $email, $phone, $address, $password, $create_at, $profile_pic);
        
        // Execute statement
        $statement->execute();
        $statement->close();

        // Set a success flag
        $_SESSION['registration_success'] = true;

        // Redirect to the login page
        header("location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Movie Review Paradise</title>
    <link rel="icon" href="../images/Logo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --dark-bg: #0a0a23;
            --card-bg: rgba(255, 255, 255, 0.95);
            --text-light: #e6e6e6;
            --accent-color: #ffd700;
            --error-color: #dc3545;
            --success-color: #198754;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--dark-bg);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            padding: 2rem 0;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--primary-gradient);
            opacity: 0.1;
            z-index: 1;
        }

        .floating-shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            opacity: 0.1;
            color: white;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            top: 15%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            top: 70%;
            right: 15%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .auth-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
        }

        .auth-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem 2.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideInUp 0.8s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-title {
            font-size: 2rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .auth-subtitle {
            color: #666;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1rem;
            position: relative;
        }

        .form-label-custom {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 1rem 1rem 1rem 3rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
            width: 100%;
        }

        .form-control-custom:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
            outline: none;
        }

        .form-control-custom.is-invalid {
            border-color: var(--error-color);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            z-index: 3;
        }

        .error-text {
            color: var(--error-color);
            font-size: 0.8rem;
            margin-top: 0.25rem;
            display: block;
        }

        .btn-custom {
            padding: 12px 30px;
            border-radius: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: none;
            width: 100%;
            margin-bottom: 1rem;
        }

        .btn-primary-custom {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-outline-custom {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-outline-custom:hover {
            background: var(--primary-gradient);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .auth-links {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
        }

        .auth-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .auth-links a:hover {
            color: #764ba2;
        }

        .back-to-home {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 3;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateX(-5px);
        }

        .progress-bar {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--primary-gradient);
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        .step-indicator {
            text-align: center;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #666;
        }

        .file-upload-area {
            border: 2px dashed #ddd;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            background: #f8f9fa;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }

        .file-upload-area.dragover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }

        .file-preview {
            margin-top: 1rem;
            text-align: center;
        }

        .file-preview img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 50%;
            border: 3px solid #667eea;
        }

        @media (max-width: 768px) {
            .auth-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }
            
            .auth-title {
                font-size: 1.5rem;
            }

            .form-control-custom {
                padding: 0.875rem 0.875rem 0.875rem 2.5rem;
            }

            .input-icon {
                left: 12px;
            }

            .form-label-custom {
                left: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <i class="fas fa-film shape" style="font-size: 3rem;"></i>
        <i class="fas fa-star shape" style="font-size: 2rem;"></i>
        <i class="fas fa-ticket-alt shape" style="font-size: 2.5rem;"></i>
    </div>

    <a href="../index.php" class="back-to-home">
        <div class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>
            Back to Home
        </div>
    </a>

    <div class="container">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <h1 class="auth-title">Join Movie Paradise</h1>
                    <p class="auth-subtitle">Create your account and start exploring amazing movies</p>
                </div>

                <div class="step-indicator">
                    Step 1 of 1 - Complete Registration
                </div>
                
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 100%;"></div>
                </div>

                <form method="post" enctype="multipart/form-data" id="registrationForm">
                    <div class="row">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">First Name *</label>
                                <div class="position-relative">
                                    <i class="fas fa-user input-icon"></i>
                                    <input class="form-control form-control-custom <?= !empty($first_name_error) ? 'is-invalid' : '' ?>" 
                                           type="text" 
                                           name="first_name" 
                                           value="<?= htmlspecialchars($first_name) ?>" 
                                           required />
                                </div>
                                <?php if (!empty($first_name_error)) { ?>
                                    <span class="error-text"><?= htmlspecialchars($first_name_error) ?></span>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">Last Name *</label>
                                <div class="position-relative">
                                    <i class="fas fa-user input-icon"></i>
                                    <input class="form-control form-control-custom <?= !empty($last_name_error) ? 'is-invalid' : '' ?>" 
                                           type="text" 
                                           name="last_name" 
                                           value="<?= htmlspecialchars($last_name) ?>" 
                                           required />
                                </div>
                                <?php if (!empty($last_name_error)) { ?>
                                    <span class="error-text"><?= htmlspecialchars($last_name_error) ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label-custom">Email Address *</label>
                        <div class="position-relative">
                            <i class="fas fa-envelope input-icon"></i>
                            <input class="form-control form-control-custom <?= !empty($email_error) ? 'is-invalid' : '' ?>" 
                                   type="email" 
                                   name="email" 
                                   value="<?= htmlspecialchars($email) ?>" 
                                   required />
                        </div>
                        <?php if (!empty($email_error)) { ?>
                            <span class="error-text"><?= htmlspecialchars($email_error) ?></span>
                        <?php } ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label-custom">Phone Number *</label>
                        <div class="position-relative">
                            <i class="fas fa-phone input-icon"></i>
                            <input class="form-control form-control-custom <?= !empty($phone_error) ? 'is-invalid' : '' ?>" 
                                   type="tel" 
                                   name="phone" 
                                   value="<?= htmlspecialchars($phone) ?>" 
                                   required />
                        </div>
                        <?php if (!empty($phone_error)) { ?>
                            <span class="error-text"><?= htmlspecialchars($phone_error) ?></span>
                        <?php } ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label-custom">Address</label>
                        <div class="position-relative">
                            <i class="fas fa-map-marker-alt input-icon"></i>
                            <input class="form-control form-control-custom <?= !empty($address_error) ? 'is-invalid' : '' ?>" 
                                   type="text" 
                                   name="address" 
                                   value="<?= htmlspecialchars($address) ?>" />
                        </div>
                        <?php if (!empty($address_error)) { ?>
                            <span class="error-text"><?= htmlspecialchars($address_error) ?></span>
                        <?php } ?>
                    </div>

                    <div class="row">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">Password *</label>
                                <div class="position-relative">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input class="form-control form-control-custom <?= !empty($password_error) ? 'is-invalid' : '' ?>" 
                                           type="password" 
                                           name="password" 
                                           required />
                                </div>
                                <?php if (!empty($password_error)) { ?>
                                    <span class="error-text"><?= htmlspecialchars($password_error) ?></span>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">Confirm Password *</label>
                                <div class="position-relative">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input class="form-control form-control-custom <?= !empty($confirm_password_error) ? 'is-invalid' : '' ?>" 
                                           type="password" 
                                           name="confirm_password" 
                                           required />
                                </div>
                                <?php if (!empty($confirm_password_error)) { ?>
                                    <span class="error-text"><?= htmlspecialchars($confirm_password_error) ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label-custom">
                            <i class="fas fa-camera me-2"></i>Profile Picture
                        </label>
                        <div class="file-upload-area" onclick="document.getElementById('profile_pic').click()">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color: #667eea;"></i>
                            <p class="mb-1">Click to upload or drag and drop</p>
                            <small class="text-muted">JPG, JPEG, PNG or GIF (Max 5MB)</small>
                            <input type="file" 
                                   name="profile_pic" 
                                   id="profile_pic" 
                                   accept="image/*" 
                                   style="display: none;" />
                        </div>
                        <div class="file-preview" id="filePreview"></div>
                        <?php if (!empty($profile_pic_error)) { ?>
                            <span class="error-text"><?= htmlspecialchars($profile_pic_error) ?></span>
                        <?php } ?>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary-custom btn-custom">
                            <i class="fas fa-user-plus me-2"></i>
                            Create Account
                        </button>

                        <a href="../index.php" class="btn btn-outline-custom btn-custom">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                    </div>
                </form>

                <div class="auth-links">
                    <p class="mb-0">Already have an account? <a href="login.php">Sign in here</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
        // Enhanced form interactions - simplified for fixed layout
        document.querySelectorAll('.form-control-custom').forEach(input => {
            // Remove error state on input
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const errorSpan = this.parentElement.querySelector('.error-text');
                if (errorSpan) {
                    errorSpan.style.display = 'none';
                }
            });
        });

        // File upload handling
        const fileInput = document.getElementById('profile_pic');
        const filePreview = document.getElementById('filePreview');
        const uploadArea = document.querySelector('.file-upload-area');

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                displayFilePreview(file);
            }
        });

        // Drag and drop functionality
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                displayFilePreview(files[0]);
            }
        });

        function displayFilePreview(file) {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    filePreview.innerHTML = `
                        <div class="text-center">
                            <img src="${e.target.result}" alt="Preview" style="max-width: 100px; max-height: 100px; border-radius: 50%; border: 3px solid #667eea;">
                            <p class="mt-2 mb-0 small text-success">
                                <i class="fas fa-check-circle me-1"></i>
                                ${file.name}
                            </p>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            }
        }

        // Password strength indicator
        const passwordInput = document.querySelector('input[name="password"]');
        const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            // You can add visual password strength indicator here
        });

        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;
            
            if (password && confirmPassword && password !== confirmPassword) {
                this.classList.add('is-invalid');
            } else if (password === confirmPassword) {
                this.classList.remove('is-invalid');
            }
        });

        function calculatePasswordStrength(password) {
            let strength = 0;
            if (password.length >= 6) strength += 1;
            if (password.match(/[a-z]/)) strength += 1;
            if (password.match(/[A-Z]/)) strength += 1;
            if (password.match(/[0-9]/)) strength += 1;
            if (password.match(/[^a-zA-Z0-9]/)) strength += 1;
            return strength;
        }

        // Form submission animation
        const form = document.getElementById('registrationForm');
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.btn-primary-custom');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Account...';
            submitBtn.disabled = true;
        });

        // Phone number formatting
        const phoneInput = document.querySelector('input[name="phone"]');
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('60')) {
                value = '+' + value;
            } else if (value.startsWith('0')) {
                // Keep as is for Malaysian format
            }
            e.target.value = value;
        });
    </script>
</body>
</html>