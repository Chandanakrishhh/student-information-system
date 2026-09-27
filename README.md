# Student Information System

A role-based web application for managing student academic records, built as an academic project. The system handles students, faculty, courses, enrollment, exams, and results through a relational MySQL schema with foreign key integrity, and enforces access control based on user role.

## Features

- **Relational database design** — 6 interconnected tables (students, faculty, courses, enrollment, exams, results) with enforced foreign key constraints
- **Role-based authentication** — session-based login with two roles:
  - **Admin**: full CRUD access to all modules
  - **Faculty**: restricted access to exam and result management only
- **SQL injection prevention** — all database queries use prepared statements (parameterized queries) instead of raw string concatenation
- **XSS protection** — user-submitted data is escaped on output using `htmlspecialchars()`
- **Referential integrity handling** — graceful error messages for foreign key violations and duplicate key attempts, rather than raw database errors

## Tech Stack

- **Backend**: PHP (procedural, mysqli)
- **Database**: MySQL (via XAMPP/MariaDB)
- **Frontend**: HTML, CSS
- **Environment**: XAMPP (Apache + MySQL)

## Modules

| Module | Description | Access |
|---|---|---|
| Manage Students | Add/edit/delete student records | Admin only |
| Manage Faculty | Add/edit/delete faculty records | Admin only |
| Manage Courses | Add/edit/delete courses, linked to faculty | Admin only |
| Manage Enrollment | Link students to courses, track attendance | Admin only |
| Manage Exams | Schedule exams per course | Admin + Faculty |
| Manage Results | Record student marks and grades per exam | Admin + Faculty |

## Setup

1. Clone the repo into your XAMPP `htdocs` folder:
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin (`localhost/phpmyadmin`), create a database named `student`, and import the schema (see `/database` folder).
4. Update `config/db_connect.php` with your MySQL credentials if different from default (`root` / no password).
5. Visit `localhost/AAT/login.php` and log in with your configured admin/faculty credentials.

## Security Notes

This project was built to demonstrate secure PHP/MySQL practices in a small-scale relational application, including protection against SQL injection and unauthorized access via session-based role checks.
