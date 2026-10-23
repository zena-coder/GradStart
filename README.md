# GradStart

GradStart is a web application that helps graduates and students find training and co-op opportunities in one place. Graduates can browse, search, filter by skills, view details, and bookmark opportunities.

Course: IT320 Practical Software Engineering, Section 76361, Group 3 (King Saud University)

## Features

Graduate:
- Register, log in, log out
- Reset a forgotten password through an emailed link
- Browse and search opportunities by title, company, specialization, or description
- Filter opportunities by skills
- View opportunity details and apply through the company link
- Bookmark opportunities and view saved ones
- View the profile (name and email) and change the password

## Administrator role

GradStart has a second role, the administrator, who manages the opportunities shown to graduates. The administrator adds new opportunities, edits existing ones, and deletes those that are no longer available, and graduates see these changes immediately. The administrator account is created by `schema.sql` together with the sample opportunities. The administrator pages are not included in this sprint, so only graduate accounts can be used for now.

## Technologies

PHP 8, MySQL, HTML, CSS, JavaScript, PHPMailer

## How to run

1. Install XAMPP or MAMP and start Apache and MySQL.
2. Copy the project files into a folder named `gradstart` inside `htdocs`.
3. Open phpMyAdmin, go to Import, choose `schema.sql`, and run it. This creates the `gradstart` database with all tables and sample data.
4. If your MySQL root password is empty like XAMPP (MAMP uses `root`), change it in `config.php` in the line that starts with `$conn = @mysqli_connect`.
5. Open `http://localhost/gradstart/login.php` (use `http://localhost:8888/gradstart/login.php` if your server uses port 8888).

## Accounts

- Graduate: create one from the Register page
- Admin:
   email : admin@gradstart.com
   password : Admin@123
  
## Password reset email

Reset links are sent by email through Gmail SMTP. To enable it, open `mail_config.php` and set `SMTP_USER` to a Gmail address and `SMTP_PASS` to a Google App Password (Google Account > Security > 2-Step Verification > App passwords).

If `mail_config.php` is left unchanged, the reset page opens directly after a registered email is entered, so the feature can be tested without any setup.

## Project structure

Authentication
- `login.php`: log in
- `register.php`: create a graduate account
- `logout.php`: end the session
- `auth.php`: redirects visitors who are not logged in to the login page

Password reset
- `reset_password.php`: request a reset link by email
- `reset_confirm.php`: choose a new password from the link
- `mailer.php`, `mail_config.php`: email sending (PHPMailer and Gmail settings)

Graduate pages
- `index.php`: browse, search, and filter opportunities
- `details.php`: opportunity details and apply link
- `saved.php`: bookmarked opportunities
- `profile.php`: name and email of the graduate
- `change_password.php`: change the password

Shared parts
- `navbar.php`, `footer.php`, `message.php`: reusable page sections and status messages
- `config.php`: database connection and helper functions

Database
- `schema.sql`: database tables and sample data

Interface and libraries
- `style.css`, `script.js`, `logo.png`: styles, scripts, and logo
- `phpmailer/`: PHPMailer library
