<?php
include "../components/database.php"; // Ensure this path is correct

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION["email"])) {
    header("location: login.php");
    exit;
}

// Initialize variables
$first_name_error = "";
$last_name_error = "";
$phone_error = "";
$address_error = "";
$profile_pic_error = "";

$error = false;

// Save changes if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the database connection
    $conn = getDatabaseConnection();
    
    // Handle profile picture upload
    if (isset($_FILES["profile_pic"]) && $_FILES["profile_pic"]["error"] == 0) {
        $allowed_ext = array("jpg", "jpeg", "png", "gif", "jfif");
        $file_name = $_FILES['profile_pic']['name'];
        $file_size = $_FILES['profile_pic']['size'];
        $file_tmp = $_FILES['profile_pic']['tmp_name'];
        
        $file_parts = explode('.', $file_name);
        $file_ext = strtolower(end($file_parts));

        if (!in_array($file_ext, $allowed_ext)) {
            $profile_pic_error = "Extension not allowed, please choose a JPG, JPEG, PNG, GIF, or JFIF file.";
            $error = true;
        }

        if ($file_size > 5097152) { // 5 MB
            $profile_pic_error = "File size must be less than 5 MB.";
            $error = true;
        }

        if (!$error) {
            $target_dir = "../uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $profile_pic = $target_dir . uniqid() . '.' . $file_ext;
            if (move_uploaded_file($file_tmp, $profile_pic)) {
                $_SESSION["profile_pic"] = $profile_pic;

                // Update profile picture in the database
                $statement = $conn->prepare("UPDATE users SET profile_pic = ? WHERE email = ?");
                $statement->bind_param("ss", $profile_pic, $_SESSION["email"]);
                $statement->execute();
                $statement->close();
            } else {
                $profile_pic_error = "Sorry, there was an error uploading your file.";
                $error = true;
            }
        }
    }

    // Validate and update other user details in the database
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];

    if (empty($first_name)) {
        $first_name_error = "First name is required.";
        $error = true;
    }

    if (empty($last_name)) {
        $last_name_error = "Last name is required.";
        $error = true;
    }

    if (!preg_match("/^(\\+?60|0)1[0-9]{1}[-]?[0-9]{7}$/", $phone)) {
        $phone_error = "Phone format is not valid.";
        $error = true;
    }

    if (!$error) {
        $statement = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ?, address = ? WHERE email = ?");
        $statement->bind_param("sssss", $first_name, $last_name, $phone, $address, $_SESSION["email"]);
        $statement->execute();
        $statement->close();

        // Update session variables
        $_SESSION["first_name"] = $first_name;
        $_SESSION["last_name"] = $last_name;
        $_SESSION["phone"] = $phone;
        $_SESSION["address"] = $address;

        header("location: profile.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Movie Review Paradise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --dark-bg: #f8f9fa;
            --card-bg: rgba(255, 255, 255, 0.98);
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --accent-color: #ffd700;
            --danger-gradient: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --border-radius: 15px;
            --shadow-soft: 0 5px 20px rgba(0, 0, 0, 0.08);
            --shadow-medium: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--dark-bg);
            color: var(--text-dark);
            line-height: 1.6;
            padding-top: 0;
        }

        /* Compact header override */
        .main-header-navbar {
            padding: 0.5rem 0 !important;
            min-height: 60px !important;
        }

        .main-header-navbar .navbar-brand {
            font-size: 1.1rem !important;
        }

        .main-header-navbar .nav-link {
            padding: 0.5rem 1rem !important;
            font-size: 0.9rem !important;
        }

        .main-header-navbar .profile-pic {
            width: 28px !important;
            height: 28px !important;
        }

        /* Compact footer override */
        footer {
            padding: 1rem 0 !important;
            margin-top: 2rem !important;
        }

        footer .container {
            padding: 0.5rem 0 !important;
        }

        footer img {
            width: 18px !important;
            height: 18px !important;
        }

        footer small {
            font-size: 0.75rem !important;
        }

        .floating-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            opacity: 0.03;
            z-index: -1;
        }

        .container {
            margin-top: 2rem;
            margin-bottom: 2rem;
            position: relative;
            z-index: 2;
        }

        .edit-profile-container {
            max-width: 700px;
            margin: 0 auto;
            background: var(--card-bg);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-medium);
            border: 1px solid rgba(0, 0, 0, 0.05);
            animation: slideInUp 0.6s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .edit-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .edit-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><path d="M0,10 Q50,0 100,10 L100,20 L0,20 Z" fill="rgba(255,255,255,0.1)"/></svg>');
            background-size: cover;
        }

        .edit-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .edit-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
            margin-bottom: 0;
        }

        .back-button {
            position: absolute;
            left: 2rem;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            z-index: 3;
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-50%) scale(1.05);
        }

        .edit-body {
            padding: 2rem;
        }

        .profile-picture-section {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-radius: var(--border-radius);
        }

        .current-profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            object-fit: cover;
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }

        .current-profile-pic:hover {
            transform: scale(1.05);
        }

        .upload-label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        /* Fixed form styling to match the image */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
            width: 100%;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .text-danger {
            font-size: 0.8rem;
            margin-top: 0.25rem;
            display: block;
        }

        /* Icon styling for labels */
        .label-icon {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .btn-custom {
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.85rem;
            min-width: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .btn-save {
            background: var(--success-gradient);
            color: white;
            box-shadow: 0 6px 20px rgba(79, 172, 254, 0.3);
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.4);
            color: white;
        }

        .btn-cancel {
            background: var(--text-muted);
            color: white;
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
            text-decoration: none;
        }

        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
            color: white;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .upload-info {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .container {
                margin-top: 1rem;
                padding: 0 1rem;
            }
            
            .edit-header {
                padding: 1.5rem 1rem;
            }
            
            .back-button {
                position: static;
                transform: none;
                margin-bottom: 1rem;
                display: inline-block;
            }
            
            .edit-title {
                font-size: 1.5rem;
                margin-top: 1rem;
            }
            
            .edit-body {
                padding: 1.5rem 1rem;
            }
            
            .form-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-custom {
                min-width: 200px;
            }

            .current-profile-pic {
                width: 100px;
                height: 100px;
            }
        }

        /* Animation for form fields */
        .form-group {
            animation: slideInLeft 0.4s ease-out both;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.15s; }
        .form-group:nth-child(3) { animation-delay: 0.2s; }
        .form-group:nth-child(4) { animation-delay: 0.25s; }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <?php include "../components/header.php"; ?>
    <div class="floating-background"></div>

    <div class="container">
        <div class="edit-profile-container">
            <div class="edit-header">
                <a href="profile.php" class="back-button">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
                <h1 class="edit-title">
                    <i class="fas fa-user-edit me-2"></i>
                    Edit Profile
                </h1>
                <p class="edit-subtitle">Update your account information</p>
            </div>

            <div class="edit-body">
                <form action="edit_profile.php" method="post" enctype="multipart/form-data">
                    <!-- Profile Picture Section -->
                    <div class="profile-picture-section">
                        <img src="<?=$_SESSION["profile_pic"]?>" alt="Profile Picture" class="current-profile-pic">
                        <div class="upload-label">Change Profile Picture</div>
                        <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*">
                        <div class="upload-info">Max file size: 5MB. Supported formats: JPG, JPEG, PNG, GIF</div>
                        <?php if($profile_pic_error): ?>
                            <span class="text-danger"><?=$profile_pic_error?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Form Fields -->
                    <div class="form-group">
                        <label for="first_name" class="form-label">
                            <i class="fas fa-user label-icon"></i>
                            First Name
                        </label>
                        <input type="text" class="form-control <?=$first_name_error ? 'is-invalid' : ''?>" 
                               id="first_name" name="first_name" value="<?=$_SESSION["first_name"]?>" required>
                        <?php if($first_name_error): ?>
                            <span class="text-danger"><?=$first_name_error?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="last_name" class="form-label">
                            <i class="fas fa-user label-icon"></i>
                            Last Name
                        </label>
                        <input type="text" class="form-control <?=$last_name_error ? 'is-invalid' : ''?>" 
                               id="last_name" name="last_name" value="<?=$_SESSION["last_name"]?>" required>
                        <?php if($last_name_error): ?>
                            <span class="text-danger"><?=$last_name_error?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">
                            <i class="fas fa-phone label-icon"></i>
                            Phone Number
                        </label>
                        <input type="text" class="form-control <?=$phone_error ? 'is-invalid' : ''?>" 
                               id="phone" name="phone" value="<?=$_SESSION["phone"]?>" 
                               placeholder="e.g., 012-3456789" required>
                        <?php if($phone_error): ?>
                            <span class="text-danger"><?=$phone_error?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label">
                            <i class="fas fa-map-marker-alt label-icon"></i>
                            Address
                        </label>
                        <input type="text" class="form-control <?=$address_error ? 'is-invalid' : ''?>" 
                               id="address" name="address" value="<?=$_SESSION["address"]?>" 
                               placeholder="Enter your full address" required>
                        <?php if($address_error): ?>
                            <span class="text-danger"><?=$address_error?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <button class="btn btn-custom btn-save" type="submit">
                            <i class="fas fa-save"></i>
                            Save Changes
                        </button>
                        <a href="profile.php" class="btn btn-custom btn-cancel">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Add loading animation to save button
        document.querySelector('.btn-save').addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
        });

        // Preview uploaded image
        document.getElementById('profile_pic').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.current-profile-pic').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Form validation feedback
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                    const errorSpan = this.parentNode.querySelector('.text-danger');
                    if (errorSpan) {
                        errorSpan.style.display = 'none';
                    }
                }
            });
        });
    </script>
</body>
</html>

<?php
include "../components/footer.php";
?>