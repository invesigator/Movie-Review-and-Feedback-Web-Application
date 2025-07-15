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
   git clone https://github.com/invesigator/Movie-Review-and-Feedback-Web-Application.git
   
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

## Demo with Screenshot
### Landing Page
<img width="1919" height="853" alt="image" src="https://github.com/user-attachments/assets/54721114-9bcc-4fa6-ad61-02130e0782ff" />

<img width="1919" height="860" alt="image" src="https://github.com/user-attachments/assets/69a6ac83-e87b-4833-a736-bdf588b58054" />

<img width="1919" height="867" alt="image" src="https://github.com/user-attachments/assets/6f897e8e-0ad2-46c0-afce-2a6133870b03" />

### Login Page
<img width="1919" height="858" alt="image" src="https://github.com/user-attachments/assets/02cbc1a1-5779-4246-99b4-5bf40fd34655" />

### Main Page
<img width="1919" height="863" alt="image" src="https://github.com/user-attachments/assets/057fd551-2176-45f8-9d00-2ac6e377eb61" />

<img width="1919" height="864" alt="image" src="https://github.com/user-attachments/assets/397f3025-03cc-43ec-89dc-ab3fbd12c202" />

### Movie Detail Page
<img width="1919" height="867" alt="image" src="https://github.com/user-attachments/assets/eac23ce7-6801-4cf0-996e-0322422c5bf3" />

<img width="1919" height="858" alt="image" src="https://github.com/user-attachments/assets/2d54f010-b2b9-47f3-b9e5-0dc74dc44e35" />

### My Profile Page
<img width="1919" height="864" alt="image" src="https://github.com/user-attachments/assets/decb409d-09de-4488-a075-c684c1036a0d" />

<img width="1919" height="862" alt="image" src="https://github.com/user-attachments/assets/2910e137-644f-4dff-8d2e-b5319050fc29" />

### Admin Panel
<img width="1919" height="861" alt="image" src="https://github.com/user-attachments/assets/83e6f7ef-33f0-48b1-ac18-4392dafc17c7" />

### Movie List
<img width="1047" height="867" alt="image" src="https://github.com/user-attachments/assets/e38bda03-520a-4a45-887f-84906acba0c6" />


## 👨‍💻 Development Team

This project was developed as part of UCCD3243 Server-Side Web Applications Development course at Universiti Tunku Abdul Rahman (UTAR).

## 📄 License

This project is developed for educational purposes as part of university coursework.

## 🤝 Contributing

This is an academic project. For educational use and reference only.

---

**Note**: This application is designed for educational purposes and demonstrates the implementation of a full-stack web application using PHP and MySQL with XAMPP local development environment.
