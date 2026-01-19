# SoundVibe Setup Guide

## Requirements
- PHP 8.0+
- MySQL 8.0+
- Apache (optional, PHP built-in server works)

## Quick Setup

### 1. Install Dependencies (Ubuntu/Pop OS)
```bash
sudo apt update
sudo apt install php php-mysql php-pdo mysql-server
```

### 2. Setup MySQL Database
```bash
# Start MySQL
sudo systemctl start mysql

# Login to MySQL
sudo mysql -u root

# Run these SQL commands:
CREATE DATABASE soundvibe;
CREATE USER 'svibe_admin'@'localhost' IDENTIFIED BY 'Svibe@2025Pwd';
GRANT ALL PRIVILEGES ON soundvibe.* TO 'svibe_admin'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import the schema
mysql -u svibe_admin -p soundvibe < sql/schema.sql
# Password: Svibe@2025Pwd
```

### 3. Start the Server
```bash
cd /path/to/drivesmart
php -S localhost:8000
```

### 4. Open in Browser
Visit: http://localhost:8000

### Default Login
- **Admin:** admin@soundvibe.com / password
- **Test User:** Register a new account

---

## Database Credentials (in config/database.php)
- Host: localhost
- Database: soundvibe
- User: svibe_admin
- Password: Svibe@2025Pwd

## If Using Different Credentials
Edit `config/database.php` and update:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'soundvibe');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

---

## Project Structure
```
├── admin/          # Admin panel pages
├── auth/           # Login, register, logout
├── assets/         # CSS, JS, images
├── config/         # Database configuration
├── includes/       # Shared PHP (header, footer, auth)
├── sql/            # Database schema
└── *.php           # Public pages
```

## Key Features
- User authentication (register/login/logout)
- Admin dashboard with CRUD for users/artists/albums/playlists
- Contact form with message management
- Search functionality
- Responsive design
