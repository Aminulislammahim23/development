# Clean Basic MVC Structure

## Project Structure

```
app/
├── models/
│   ├── db.php          # Database connection
│   ├── userModel.php   # User-related database operations
│   ├── courseModel.php # Course-related database operations
│   └── paymentModel.php # Payment-related database operations
├── views/
│   ├── admin/
│   │   └── dashboard.php
│   ├── instructor/
│   │   └── dashboard.php
│   ├── student/
│   │   ├── dashboard.php
│   │   ├── courseView.php
│   │   ├── invoices.php
│   │   └── takeQuiz.php
│   └── auth/
│       ├── login.php
│       ├── register.php
│       └── chooseRole.php
├── controllers/
│   ├── admin_controller.php     # Admin operations
│   ├── instructor_controller.php # Instructor operations
│   └── student_controller.php   # Student operations
└── assets/
    ├── css/           # Stylesheets
    ├── js/            # JavaScript files
    └── images/        # Images and media
```

## Core MVC Components

### Models
Handle all database operations:
- **userModel.php**: User authentication and management
- **courseModel.php**: Course creation and management
- **paymentModel.php**: Payment processing

### Views
Handle presentation layer:
- Organized by user roles (admin, instructor, student)
- Clean separation of HTML/CSS from business logic

### Controllers
Handle request processing:
- **admin_controller.php**: Admin-specific operations
- **instructor_controller.php**: Instructor-specific operations
- **student_controller.php**: Student-specific operations

## Router

The main `index.php` file handles routing:
- Maps URL paths to controllers and actions
- Supports clean URLs like `/admin/dashboard`
- Handles 404 errors appropriately

## Benefits

1. **Minimal**: Only essential MVC components
2. **Clear Separation**: Models, views, and controllers have distinct roles
3. **Simple**: No unnecessary complexity
4. **Maintainable**: Easy to understand and modify
5. **Organized**: Clean directory structure