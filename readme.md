# Event Management System

A PHP-based event management system with user authentication, event creation, and attendee management.

## Features

- User Authentication (Login/Register)
- Event Management (Create, Read, Update, Delete)
- Attendee Registration
- Admin Dashboard
- Export Attendee List
- Responsive Bootstrap 5 UI
- Form Validation
- Security Features

## Prerequisites

- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server
- Composer
- mod_rewrite enabled

## Installation

1. Clone the repository
```bash
git clone https://github.com/touhi13/event-management.git
cd event-management
```

2. Install dependencies
```bash
composer install
```

3. Create database and import schema
```sql
CREATE DATABASE event_management;
USE event_management;

-- Create users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create events table
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    event_date DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    max_capacity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create attendees table
CREATE TABLE attendees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendee (event_id, email)
);

-- Add initial admin user (password: password)
INSERT INTO users (username, email, password, is_admin) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE);
```

4. Configure your database
Create `config/database.php`:
```php
<?php
return [
    'host' => 'localhost',
    'database' => 'event_management',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
```

5. Configure Apache
Create `.htaccess` in your project root:
```apache
RewriteEngine On
RewriteBase /event-management/

# Redirect all requests to public directory
RewriteCond %{THE_REQUEST} /public/([^\s?]) [NC]
RewriteRule ^ %1 [L,NE,R=302]
RewriteRule ^((?!public/).)$ public/$1 [L,NC]

# Prevent directory listing
Options -Indexes

# Deny access to sensitive files
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

<FilesMatch "(composer\.json|composer\.lock|package\.json|package-lock\.json|\.gitignore)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Protect sensitive directories
<IfModule mod_rewrite.c>
    RewriteRule ^(config|src|templates|vendor) - [F,L]
</IfModule>
```

6. Set permissions
```bash
chmod -R 755 .
chmod -R 777 storage/logs
```

7. Create required directories
```bash
mkdir -p public/assets/css
mkdir -p public/assets/js
mkdir -p storage/logs
```

## Project Structure
```plaintext
event-management/
├── config/
│   ├── app.php
│   ├── auth.php
│   └── database.php
├── public/
│   ├── assets/
│   │   ├── css/
│   │   └── js/
│   ├── .htaccess
│   └── index.php
├── src/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── EventController.php
│   │   └── AttendeeController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Event.php
│   │   └── Attendee.php
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   └── AdminMiddleware.php
├── templates/
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── events/
│   │   ├── create.php
│   │   ├── edit.php
│   │   ├── index.php
│   │   └── view.php
│   ├── attendees/
│   │   └── register.php
│   ├── layout/
│   │   ├── header.php
│   │   └── footer.php
├── vendor/
├── .htaccess
├── composer.json
└── README.md
```

## Usage

1. Access the application

2. Default admin credentials:
   - Email: admin@example.com
   - Password: password

3. Features:
   - Create and manage events
   - Register for events
   - View event details and attendees
   - Export attendee lists (admin only)
   - Manage user permissions

## Security Features

- Password hashing using bcrypt
- PDO prepared statements for SQL injection prevention
- HTML escaping for XSS protection
- CSRF token validation
- Input validation and sanitization
- Secure session handling
- Protected sensitive routes
- File access restrictions

