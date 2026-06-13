# HACK KUET Portfolio

Portfolio and admin panel for HACK KUET, the Hardware Acceleration Club of KUET.

This is a PHP and MySQL project designed to run locally with XAMPP. It includes public pages for club information, events, projects, blogs, membership, and contact forms, plus an admin dashboard for managing the site content.

## Features

- Public home, about, event, project, blog, contact, and membership pages
- Admin authentication and dashboard
- Content management for events, projects, blogs, members, executives, and contact information
- Blog submission and contact form handling
- MySQL database schema and seed content
- Image uploads for admin-managed content

## Requirements

- XAMPP or another PHP/MySQL stack
- PHP with PDO MySQL enabled
- MySQL or MariaDB
- Web browser

## Setup

1. Place the project folder inside your XAMPP `htdocs` directory.

   Example:

   ```text
   C:\xampp\htdocs\Hack-Club
   ```

2. Start Apache and MySQL from the XAMPP Control Panel.

3. Create and import the database.

   Open phpMyAdmin or a MySQL client and import:

   ```text
   database/schema.sql
   ```

   Optional seed files are available in the `database` folder:

   ```text
   database/event_seed_content.sql
   database/activity_seed_content.sql
   database/blog_seed_content.sql
   ```

4. Check the database configuration in:

   ```text
   admin/includes/config.php
   ```

   Default settings:

   ```php
   DB_HOST = localhost
   DB_NAME = hack_kuet
   DB_USER = root
   DB_PASS =
   ```

5. Open the site in your browser:

   ```text
   http://localhost/Hack-Club/home.php
   ```

6. Open the admin panel:

   ```text
   http://localhost/Hack-Club/admin/
   ```

## Project Structure

```text
admin/              Admin dashboard, modules, APIs, uploads, and shared includes
database/           Database schema and seed SQL files
json/               Local editor/config files
home.php            Main public website page
public-data.php     Shared public data access helpers
style.css           Public site styles
main.js             Public site scripts
theme-toggle.js     Theme toggle script
```

## Notes

- Uploaded admin files are stored under `admin/uploads/`.
- The database name used by default is `hack_kuet`.
- If the project folder name changes, update the browser URL accordingly.

