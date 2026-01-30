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

### Migration notes (adding `full_name`)

If you have an existing database from an earlier version, run these SQL commands to add the `full_name` column and populate it from `first_name`/`last_name`:

```sql
ALTER TABLE users ADD COLUMN full_name VARCHAR(150) NULL;
UPDATE users SET full_name = TRIM(CONCAT_WS(' ', NULLIF(first_name, ''), NULLIF(last_name, ''))) WHERE full_name IS NULL OR full_name = '';
```

The application has been updated to prefer `full_name` where present. New registrations will set `full_name` automatically.

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
