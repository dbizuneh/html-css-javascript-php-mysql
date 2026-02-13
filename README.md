# Student Management System (PHP + MySQL)

A professional PHP student management system with:
- Add students
- Delete students
- Persistent storage in **MySQL**

## Requirements
- PHP 8+
- MySQL 8+

## Setup
1. Create schema/table:
   ```bash
   mysql -u root -p < sql/schema.sql
   ```
2. Configure environment variables (or export them in your shell):
   ```bash
   cp .env.example .env
   export DB_HOST=127.0.0.1
   export DB_PORT=3306
   export DB_NAME=student_management
   export DB_USER=root
   export DB_PASS=your_password
   ```
3. Run the app:
   ```bash
   php -S 0.0.0.0:8080 -t public
   ```
4. Open `http://localhost:8080`.

## Notes
- `src/StudentController.php` handles validation, CSRF, and request routing.
- `src/StudentRepository.php` handles all SQL queries.
- `src/Database.php` centralizes PDO connection setup.
