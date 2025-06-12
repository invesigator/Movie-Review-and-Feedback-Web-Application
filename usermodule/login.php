<?php
session_start();

if (isset($_SESSION['email'])) {
    if ($_SESSION['role'] == 'admin') {
        header("location: ../admin/admin.php");
    } else {
        header("location: ../moviemodule/movie.php");
    }
    exit;
}

$email = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email and Password are required!";
    } else {
        include "../components/database.php";
        $dbConnection = getDatabaseConnection();

        $statement = $dbConnection->prepare(
            "SELECT id, first_name, last_name, phone, address, password, role, create_at, profile_pic FROM users WHERE email = ?"
        );

        $statement->bind_param('s', $email);
        $statement->execute();
        $statement->bind_result($id, $first_name, $last_name, $phone, $address, $stored_password, $role, $create_at, $profile_pic);

        if ($statement->fetch() && password_verify($password, $stored_password)) {
            // Store data in session variables
            $_SESSION['id'] = $id;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['email'] = $email;
            $_SESSION['phone'] = $phone;
            $_SESSION['address'] = $address;
            $_SESSION['role'] = $role;
            $_SESSION['create_at'] = $create_at;
            $_SESSION['profile_pic'] = $profile_pic;
            
            // Check if 'Remember Me' is checked
            if (isset($_POST['remember_me'])) {
                $cookie_value = json_encode(['email' => $email, 'password' => $stored_password]);
                setcookie('remember_me', $cookie_value, time() + (30 * 24 * 60 * 60), '/');
            }

            // Redirect based on role
            if ($role == 'admin') {
                header("location: ../admin/admin.php");
            } else {
                header("location: ../moviemodule/movie.php");
            }
            exit;
        } else {
            $error = "Email or Password invalid";
        }

        $statement->close();
    }
}

// Check for 'Remember Me' cookie
if (isset($_COOKIE['remember_me']) && !isset($_SESSION['email'])) {
    $cookie_data = json_decode($_COOKIE['remember_me'], true);

    if ($cookie_data && isset($cookie_data['email'], $cookie_data['password'])) {
        $email = $cookie_data['email'];
        $hashed_password = $cookie_data['password'];

        include "../components/database.php";
        $dbConnection = getDatabaseConnection();

        $stmt = $dbConnection->prepare("SELECT id, first_name, last_name, phone, address, role, create_at, profile_pic FROM users WHERE email = ? AND password = ?");
        $stmt->bind_param('ss', $email, $hashed_password);
        $stmt->execute();
        $stmt->bind_result($id, $first_name, $last_name, $phone, $address, $role, $create_at, $profile_pic);

        if ($stmt->fetch()) {
            $_SESSION['id'] = $id;
            $_SESSION['email'] = $email;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['phone'] = $phone;
            $_SESSION['address'] = $address;
            $_SESSION['role'] = $role;
            $_SESSION['create_at'] = $create_at;
            $_SESSION['profile_pic'] = $profile_pic;

            // Redirect based on role
            if ($role == 'admin') {
                header("location: ../admin/admin.php");
            } else {
                header("location: ../moviemodule/movie.php");
            }
            exit;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Movie Review Paradise</title>
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
            display: flex;
            align-items: center;
            position: relative;
            overflow-x: hidden;
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
            max-width: 450px;
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
            margin-bottom: 1.5rem;
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
            font-size: 1rem;
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

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            z-index: 3;
        }

        .alert-custom {
            border: none;
            border-radius: 15px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            background: rgba(220, 53, 69, 0.1);
            color: var(--error-color);
            border-left: 4px solid var(--error-color);
        }

        .form-check-custom {
            margin-bottom: 1.5rem;
        }

        .form-check-input-custom {
            border-radius: 6px;
            border: 2px solid #ddd;
        }

        .form-check-input-custom:checked {
            background-color: #667eea;
            border-color: #667eea;
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
            position: absolute;
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

        @media (max-width: 768px) {
            .auth-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }
            
            .auth-title {
                font-size: 1.5rem;
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
                    <h1 class="auth-title">Welcome Back</h1>
                    <p class="auth-subtitle">Sign in to continue your movie journey</p>
                </div>

                <?php if (!empty($error)) { ?>
                    <div class="alert alert-custom" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php } ?>

                <form method="post">
                    <div class="form-group">
                        <label class="form-label-custom">Email Address</label>
                        <div class="position-relative">
                            <i class="fas fa-envelope input-icon"></i>
                            <input class="form-control form-control-custom" 
                                   type="email" 
                                   name="email" 
                                   value="<?= htmlspecialchars($email) ?>" 
                                   required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label-custom">Password</label>
                        <div class="position-relative">
                            <i class="fas fa-lock input-icon"></i>
                            <input class="form-control form-control-custom" 
                                   type="password" 
                                   name="password" 
                                   required />
                        </div>
                    </div>

                    <div class="form-check form-check-custom">
                        <input type="checkbox" 
                               class="form-check-input form-check-input-custom" 
                               name="remember_me" 
                               id="rememberMe">
                        <label class="form-check-label" for="rememberMe">
                            Remember me for 30 days
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary-custom btn-custom">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Sign In
                    </button>

                    <a href="../index.php" class="btn btn-outline-custom btn-custom">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </a>
                </form>

                <div class="auth-links">
                    <p class="mb-0">Don't have an account? <a href="register.php">Create one here</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
        // Enhanced form interactions
        document.querySelectorAll('.form-control-custom').forEach(input => {
            // No additional JavaScript needed for the fixed layout
        });

        // Form validation animation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.btn-primary-custom');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>