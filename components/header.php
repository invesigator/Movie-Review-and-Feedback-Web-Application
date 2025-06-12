<?php
// Initialize the session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$authenticated = false;
$username = 'Guest';
$isAdmin = false;
$profile_pic = '../images/default-profile.jpg';

if (isset($_SESSION['email'])) {
    $authenticated = true;
    $first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Unknown';
    $last_name = isset($_SESSION['last_name']) ? $_SESSION['last_name'] : 'User';
    $username = $first_name . ' ' . $last_name;
    
    if (isset($_SESSION['profile_pic']) && !empty($_SESSION['profile_pic'])) {
        $profile_pic = $_SESSION['profile_pic'];
    }
    
    if (isset($_SESSION['role'])) {
        $isAdmin = ($_SESSION['role'] === 'admin');
    }
}

$basePath = '/UCCD3243_G18/';
?>

<!-- Header styles to ensure compatibility -->
<style>
.main-header-navbar {
    background-color: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6 !important;
    box-shadow: 0 2px 4px rgba(0,0,0,.1) !important;
    position: relative !important;
    z-index: 1050 !important;
    margin-bottom: 0 !important;
}

.main-header-navbar .navbar-brand {
    font-weight: 700 !important;
    color: #333 !important;
    text-decoration: none !important;
}

.main-header-navbar .navbar-brand:hover {
    color: #667eea !important;
}

.main-header-navbar .nav-link {
    color: #333 !important;
    font-weight: 500 !important;
    text-decoration: none !important;
}

.main-header-navbar .nav-link:hover {
    color: #667eea !important;
}

.main-header-navbar .dropdown-menu {
    border: 1px solid #dee2e6 !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    border-radius: 8px !important;
    background-color: white !important;
    z-index: 1060 !important;
}

.main-header-navbar .dropdown-item {
    color: #333 !important;
    text-decoration: none !important;
}

.main-header-navbar .dropdown-item:hover {
    background-color: #f8f9fa !important;
    color: #667eea !important;
}

.main-header-navbar .dropdown-header {
    color: #666 !important;
    font-weight: 600 !important;
}

.main-header-navbar .text-danger:hover {
    color: #dc3545 !important;
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.main-header-navbar .profile-pic {
    border: 2px solid #dee2e6 !important;
    border-radius: 50% !important;
    object-fit: cover !important;
}

/* Ensure dropdown toggle works */
.main-header-navbar .dropdown-toggle::after {
    display: inline-block;
    margin-left: 0.255em;
    vertical-align: 0.255em;
    content: "";
    border-top: 0.3em solid;
    border-right: 0.3em solid transparent;
    border-bottom: 0;
    border-left: 0.3em solid transparent;
}

.main-header-navbar .dropdown-toggle:empty::after {
    margin-left: 0;
}
</style>

<!-- Header HTML -->
<nav class="navbar navbar-expand-lg main-header-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?php echo $basePath; ?>moviemodule/movie.php">
            <img src="../images/Logo.jpg" width="30" height="30" class="d-inline-block align-top me-2" alt="Logo" onerror="this.style.display='none'"> 
            <strong>UMOVIE</strong>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $isAdmin ? $basePath . 'admin/admin.php' : $basePath . 'moviemodule/movie.php'; ?>">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </li>

                <?php if ($isAdmin) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>moviemodule/movie.php">
                            <i class="fas fa-film me-1"></i>Movie Interface
                        </a>
                    </li>
                <?php } ?>
            </ul>

            <?php if ($authenticated) { ?>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="userDropdown">
                            <img src="<?php echo htmlspecialchars($profile_pic); ?>"
                                 alt="Profile Picture" 
                                 class="profile-pic me-2" 
                                 width="32" 
                                 height="32"
                                 onerror="this.src='../images/default-profile.jpg'">
                            <?php echo htmlspecialchars($username); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <h6 class="dropdown-header">
                                    <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($username); ?>
                                </h6>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?php echo $basePath; ?>usermodule/profile.php">
                                    <i class="fas fa-user-edit me-2"></i>Profile
                                </a>
                            </li>
                            <?php if ($isAdmin) { ?>
                                <li>
                                    <a class="dropdown-item" href="<?php echo $basePath; ?>admin/admin.php">
                                        <i class="fas fa-cog me-2"></i>Admin Panel
                                    </a>
                                </li>
                            <?php } ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?php echo $basePath; ?>usermodule/logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            <?php } else { ?>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>usermodule/login.php">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>usermodule/register.php">
                            <i class="fas fa-user-plus me-1"></i>Register
                        </a>
                    </li>
                </ul>
            <?php } ?>
        </div>
    </div>
</nav>