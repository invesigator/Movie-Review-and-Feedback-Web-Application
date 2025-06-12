# Movie Review Paradise - UCCD3243 Group Assignment

A comprehensive movie review and feedback web application built with PHP and MySQL, featuring a modern, responsive design and complete user management system.

## 🎬 Project Overview

Movie Review Paradise is a platform where users can discover movies, read reviews, and share their own opinions about films. The application implements a 3-tier architecture with a clean, modern interface and robust backend functionality.

## 🚀 Features

### 🎭 Movies Module
- Browse movie collection with advanced filtering
- Search movies by title, genre, and release year
- View detailed movie information (poster, director, genre, classification)
- Responsive movie cards with hover effects
- Admin panel for complete movie management (CRUD operations)

### 📝 Feedback/Discussion Module
- Write and submit movie reviews with star ratings
- Edit and delete own reviews
- View all reviews for each movie
- Real-time rating aggregation and statistics
- Interactive star rating system

### 👤 User Module
- User registration with profile picture upload
- Secure login with "Remember Me" functionality
- Profile management and editing
- Account deletion with confirmation
- Session management and authentication

### ⭐ Ratings Module
- 5-star rating system
- Average rating calculation and display
- Rating distribution visualization
- Individual rating breakdown (5-star, 4-star, etc.)
- Progress bars showing rating percentages

## 🛠️ Technical Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Framework**: Bootstrap 5.3.3
- **Icons**: Font Awesome 6.4.0
- **Fonts**: Google Fonts (Poppins)
- **Server**: XAMPP (Apache + MySQL + phpMyAdmin)

## 🏗️ Architecture

The application follows a 3-tier architecture:

1. **Presentation Layer**: HTML, CSS, JavaScript with Bootstrap
2. **Application Layer**: PHP business logic and session management
3. **Data Layer**: MySQL database with PDO connections

## 📁 Project Structure

```
├── admin/
│   ├── admin.php                # Admin dashboard and movie management
│   └── admin_style.css          # Admin panel styling
├── components/
│   ├── header.php               # Navigation header component
│   ├── footer.php               # Footer component
│   ├── database.php             # Main database connection
│   ├── movie_database.php       # Movie-specific database functions
│   └── comment_database.php     # Comment/rating database functions
├── moviemodule/
│   ├── movie.php                # Movie listing page
│   ├── movie_details.php        # Individual movie details and reviews
│   ├── submit_rating.php        # Rating submission handler
│   ├── edit_review.php          # Review editing interface
│   ├── delete_review.php        # Review deletion handler
│   ├── m_style.css              # Movie module styling
│   └── md_style.css             # Movie details styling
├── usermodule/
│   ├── login.php                # User login
│   ├── register.php             # User registration
│   ├── profile.php              # User profile display
│   ├── edit_profile.php         # Profile editing
│   ├── logout.php               # Logout handler
│   ├── delete_account.php       # Account deletion
│   └── goodbye.php              # Account deletion confirmation
├── images/                      # Movie posters and assets
├── uploads/                     # User profile pictures
└── index.php                    # Landing page
```

## 🗄️ Database Schema

### Tables

1. **users**
   - `id` (Primary Key)
   - `first_name`, `last_name`
   - `email` (Unique)
   - `phone`, `address`
   - `password` (Hashed)
   - `role` (user/admin)
   - `profile_pic`
   - `create_at`

2. **movies**
   - `movie_id` (Primary Key)
   - `title`, `genre`
   - `release_date`
   - `director`
   - `classification`
   - `poster_url`

3. **comments_ratings**
   - `feedback_id` (Primary Key)
   - `movie_id` (Foreign Key)
   - `user_id` (Foreign Key)
   - `comment`
   - `rating` (1-5 stars)
   - `date_posted`

## 🚀 Installation & Setup with XAMPP

### Prerequisites
- XAMPP (includes Apache, MySQL, PHP, and phpMyAdmin)
- Web browser
- IDE

### Installation Steps

1. **Download and Install XAMPP**
   - Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Install XAMPP in the default location (usually `C:\xampp` on Windows)

2. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Start **Apache** and **MySQL** services
   - Ensure both services show "Running" status

3. **Clone/Download the Project**
   ```bash
   # If using Git
   git clone https://github.com/yourusername/movie-review-paradise.git
   
   # Or download and extract the ZIP file
   ```

4. **Deploy Project to XAMPP**
   - Copy the entire project folder to `xampp/htdocs/`

5. **Database Setup using phpMyAdmin**
   - Open web browser and go to `http://localhost/phpmyadmin`
   - Click "New" to create a new database
   - Database name: `umovie_db`
   - Click "Create"

6. **Create Database Tables**
   - Select the `umovie_db` database
   - Click on "Import" tab
   - Choose the database file

7. **Access the Application**
   - Open web browser
   - Navigate to: `http://localhost/Movie-Review-and-Feedback-Web-Application/`
   - You should see the Movie Review Paradise landing page

## 👥 User Roles

### Regular Users
- Browse and search movies
- View movie details and reviews
- Create, edit, and delete own reviews
- Rate movies (1-5 stars)
- Manage personal profile

### Admin Users
- All user permissions
- Add, edit, and delete movies
- Manage all user reviews
- Access admin dashboard
- Upload movie posters

## 👨‍💻 Development Team

This project was developed as part of UCCD3243 Server-Side Web Applications Development course at Universiti Tunku Abdul Rahman (UTAR).

## 📄 License

This project is developed for educational purposes as part of university coursework.

## 🤝 Contributing

This is an academic project. For educational use and reference only.

---

**Note**: This application is designed for educational purposes and demonstrates the implementation of a full-stack web application using PHP and MySQL with XAMPP local development environment.
