# SoundVibe Music Streaming Web App

A modern, responsive music streaming web application built with PHP, MySQL, HTML5, CSS3, and vanilla JavaScript.

## Features

- 🎵 **Music Streaming Platform** - Browse songs, artists, albums, playlists
- 🔒 **Authentication** - Register, login, logout, role-based access (admin/member/normal)
- 🛠️ **Admin Panel** - Dashboard, manage users, artists, albums, playlists, messages
- 📑 **Legal Pages** - Privacy Policy, Terms & Conditions
- 🔍 **Search** - Full-text search for artists, albums, playlists
- 📱 **Fully Responsive** - Works perfectly on desktop, tablet, and mobile
- ♿ **Accessible** - WCAG 2.1 AA compliant
- 🌐 **SEO & Security** - Sitemap, robots.txt, .htaccess, structured data

## Pages

- **Home** (`index.php`) - Landing page with hero, features, trending tracks
- **About** (`about.php`) - Mission, story, values, team
- **Playlists** (`playlist.php`) - Curated playlists, genre browsing
- **Artists** (`artists.php`) - Featured artists, follow counts
- **Gallery** (`media.php`) - Album covers, interactive modal
- **Contact** (`contact.php`) - Contact form, FAQ
- **Search** (`search.php`) - Search artists, albums, playlists
- **Profile** (`profile.php`) - Edit info, change password, delete account
- **Admin** (`/admin/`) - Dashboard, users, artists, albums, playlists, messages
- **Legal** (`privacy.php`, `terms.php`) - Privacy Policy, Terms & Conditions
- **Error** (`404.php`) - Custom error page

## Technology Stack

- **PHP 8.x** - Backend logic, database integration
- **MySQL 8.x** - Relational database
- **HTML5/CSS3/JS** - Frontend, responsive design
- **Apache** - (or PHP built-in server)

## Setup & Deployment

See [SETUP.md](SETUP.md) for full instructions:
- Install PHP & MySQL
- Import `sql/schema.sql` to create database
- Configure credentials in `config/database.php`
- Start server: `php -S localhost:8000`
- Default admin: `admin@soundvibe.com` / `password`

## Project Structure

```
drivesmart/
├── admin/          # Admin panel pages
├── auth/           # Login, register, logout
├── assets/         # CSS, JS, images
├── config/         # Database configuration
├── includes/       # Shared PHP (header, footer, auth)
├── sql/            # Database schema
├── .htaccess       # Apache config
├── robots.txt      # Crawler rules
├── sitemap.php     # XML sitemap
├── *.php           # Public pages
├── README.md       # This file
├── SETUP.md        # Setup guide
```

## Key Features
- User authentication (register/login/logout)
- Admin dashboard with CRUD for users/artists/albums/playlists
- Contact form with message management
- Search functionality
- Responsive design
- Privacy & Terms legal pages
- SEO: sitemap, robots.txt, structured data

## License

This project is created for educational purposes as part of ICT726 Web Development Assignment 4.

## Contact

For questions or support, please reach out to:
- Email: support@soundvibe.com
- Phone: +1 (234) 567-890

---

**SoundVibe** - Your Music, Your Vibe ♪
