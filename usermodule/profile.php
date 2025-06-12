<?php
include "../components/header.php";

//Check if the user is logged in, if not redirect to login page
if(!isset($_SESSION["email"])){
    header("location: /login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Movie Review Paradise</title>
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

        .profile-container {
            max-width: 900px;
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

        .profile-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><path d="M0,10 Q50,0 100,10 L100,20 L0,20 Z" fill="rgba(255,255,255,0.1)"/></svg>');
            background-size: cover;
        }

        .profile-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .profile-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
            margin-bottom: 0;
        }

        .profile-picture-container {
            position: relative;
            margin: 1.5rem 0 1rem;
            z-index: 2;
        }

        .profile-picture {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .profile-picture:hover {
            transform: scale(1.05);
        }

        .member-badge {
            display: inline-flex;
            align-items: center;
            background: var(--accent-color);
            color: #333;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 0.5rem;
            box-shadow: 0 3px 10px rgba(255, 215, 0, 0.3);
        }

        .member-badge i {
            margin-right: 0.4rem;
        }

        .profile-body {
            padding: 2rem;
        }

        .profile-info {
            margin-bottom: 2rem;
        }

        .info-row {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f3f4;
            transition: all 0.3s ease;
        }

        .info-row:hover {
            background: rgba(102, 126, 234, 0.03);
            border-radius: 8px;
            margin: 0 -0.5rem;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-gradient);
            color: white;
            border-radius: 10px;
            margin-right: 1rem;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
            margin-bottom: 0.2rem;
        }

        .info-value {
            font-size: 0.95rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .profile-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-custom {
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.85rem;
            min-width: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            text-decoration: none;
        }

        .btn-edit {
            background: var(--success-gradient);
            color: white;
            box-shadow: 0 6px 20px rgba(79, 172, 254, 0.3);
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.4);
            color: white;
        }

        .btn-delete {
            background: var(--danger-gradient);
            color: white;
            box-shadow: 0 6px 20px rgba(255, 65, 108, 0.3);
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 65, 108, 0.4);
            color: white;
        }

        @media (max-width: 768px) {
            .container {
                margin-top: 1rem;
                padding: 0 1rem;
            }
            
            .profile-header {
                padding: 1.5rem 1rem;
            }
            
            .profile-title {
                font-size: 1.5rem;
            }
            
            .profile-body {
                padding: 1.5rem 1rem;
            }
            
            .info-row {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
                padding: 1rem 0;
            }
            
            .info-icon {
                margin-right: 0;
                margin-bottom: 0.5rem;
            }
            
            .profile-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-custom {
                min-width: 200px;
            }
        }

        /* Animation delays for info rows */
        .info-row:nth-child(1) { animation: slideInLeft 0.4s ease-out 0.1s both; }
        .info-row:nth-child(2) { animation: slideInLeft 0.4s ease-out 0.15s both; }
        .info-row:nth-child(3) { animation: slideInLeft 0.4s ease-out 0.2s both; }
        .info-row:nth-child(4) { animation: slideInLeft 0.4s ease-out 0.25s both; }
        .info-row:nth-child(5) { animation: slideInLeft 0.4s ease-out 0.3s both; }
        .info-row:nth-child(6) { animation: slideInLeft 0.4s ease-out 0.35s both; }

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
    <div class="floating-background"></div>

    <div class="container">
        <div class="profile-container">
            <div class="profile-header">
                <h1 class="profile-title">
                    <i class="fas fa-user-circle me-2"></i>
                    My Profile
                </h1>
                <p class="profile-subtitle">Manage your account information and preferences</p>
                
                <div class="profile-picture-container">
                    <img src="<?=$_SESSION["profile_pic"]?>" alt="Profile Picture" class="profile-picture">
                </div>
                
                <div class="member-badge">
                    <i class="fas fa-crown"></i>
                    Movie Enthusiast
                </div>
            </div>

            <div class="profile-body">
                <div class="profile-info">
                    <div class="info-row">
                        <div class="info-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">First Name</div>
                            <div class="info-value"><?=$_SESSION["first_name"]?></div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Last Name</div>
                            <div class="info-value"><?=$_SESSION["last_name"]?></div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Email Address</div>
                            <div class="info-value"><?=$_SESSION["email"]?></div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Phone Number</div>
                            <div class="info-value"><?=$_SESSION["phone"]?></div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Address</div>
                            <div class="info-value"><?=$_SESSION["address"]?></div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Member Since</div>
                            <div class="info-value"><?=date("F j, Y", strtotime($_SESSION["create_at"]))?></div>
                        </div>
                    </div>
                </div>

                <div class="profile-actions">
                    <a href="edit_profile.php" class="btn btn-custom btn-edit">
                        <i class="fas fa-edit"></i>
                        Edit Profile
                    </a>
                    <button class="btn btn-custom btn-delete" type="button" id="delete">
                        <i class="fas fa-trash-alt"></i>
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById("delete").addEventListener("click", function() {
            if (confirm("Are you sure you want to delete your account? This action cannot be undone.")) {
                window.location.href = "delete_account.php";
            }
        });

        // Add loading animation to buttons
        document.querySelectorAll('.btn-custom').forEach(button => {
            button.addEventListener('click', function() {
                if (this.id !== 'delete' && this.tagName === 'A') {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
                }
            });
        });
    </script>
</body>
</html>

<?php
include "../components/footer.php";
?>