# Enhanced PHP MySQL CRUD Application

## Student Record Management System

A modern, feature-rich student record management system built with PHP, MySQL, Bootstrap 5, and AJAX.

### Features

- **CRUD Operations**: Create, Read, Update, Delete student records
- **Advanced Table Features**:
  - Real-time sorting on all columns
  - Advanced filtering and search
  - Customizable pagination (5, 10, 25, 50, 100 records per page)
  - Loading indicators for better UX
- **Modern UI**: Clean, responsive design with Bootstrap 5
- **AJAX Integration**: Smooth user experience without page reloads
- **Grade Calculation**: Automatic percentage, grade, and remarks calculation
- **Data Validation**: Client-side and server-side validation

### Technologies Used

- PHP 7.4+
- MySQL
- Bootstrap 5
- jQuery
- AJAX
- Font Awesome Icons

### Setup Instructions

1. Import the database schema
2. Update database connection in `config/connect.php`
3. Access the application through your web server

### File Structure

```
├── config/
│   ├── connect.php          # Database connection
│   └── utils.php           # Utility functions
├── api/
│   └── students.php        # API endpoints
├── assets/
│   ├── css/
│   │   └── style.css       # Custom styles
│   └── js/
│       └── app.js          # JavaScript functionality
├── index.php               # Main listing page
├── create.php              # Add new student
├── update.php              # Edit student record
└── delete.php              # Delete student record
```
