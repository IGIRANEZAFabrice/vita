# Database Configuration

## Overview
This project uses environment variables defined in `.htaccess` for database configuration.

## Configuration

### .htaccess Settings
The database credentials are stored in the `.htaccess` file in the root directory:

```apache
SetEnv DB_USERNAME root
SetEnv DB_HOST localhost
SetEnv DB_NAME ecomercedb
SetEnv DB_PASSWORD
```

### Database Connection
The database connection is established in `config/db.php` and is automatically included in `index.php`.

## Usage

### In Your PHP Files
The database connection (`$conn`) is available globally after being included from `index.php`.

Example:
```php
// Query the database
$sql = "SELECT * FROM products WHERE is_active = 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo $row['product_name'];
    }
}
```

### URL Structure
The website uses a clean URL structure with query parameters:

- Home: `index.php?page=home` or just `index.php`
- About: `index.php?page=about`
- Contact: `index.php?page=contact`
- Products: `index.php?page=products`
- Product Details: `index.php?page=productOpen&id=123`
- Training: `index.php?page=training`
- Quote: `index.php?page=quote`
- Cart: `index.php?page=cart`
- Login: `index.php?page=login`

### Adding New Pages

1. Create a new PHP file in the `pages/` directory (e.g., `pages/newpage.php`)
2. Add the page name to the `$allowed_pages` array in `index.php`
3. Update the navigation in `include/header.php` if needed

Example:
```php
// In index.php
$allowed_pages = [
    'home',
    'about',
    'newpage',  // Add your new page here
    // ... other pages
];
```

## Database Schema
The database schema is defined in `ecomerce.sql`. Make sure to import this file into your MySQL database.

## Security Notes
- The `.htaccess` file should not be accessible from the web
- Always sanitize user input before using in SQL queries
- Use prepared statements for dynamic queries
- Keep database credentials secure and never commit them to version control in production

