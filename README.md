Perfect —  
I'll add a clean new section to your `README.md` about `release.sh` and explain it properly.

Here’s the **updated complete README** for you:

---

# 📄 `README.md`

# 📚 Book Management Plugin

A WordPress plugin to manage Books using a custom post type and a custom database table.  
Built with [Rabbit Framework](https://github.com/veronalabs/rabbit) and organized using service providers, repositories, and managers.

---

## ✨ Features

- Registers a **Custom Post Type** called "Book".
- Adds **two custom taxonomies**: "Publishers" and "Authors".
- Creates a **custom database table** `books_info` to store ISBNs.
- Adds a **meta box** for ISBN input when editing a Book.
- Displays ISBNs in a **custom admin page** and inside the Book post type list.
- Fully **multilingual ready** (`book-management` text domain).
- Structured for **unit testing** with PHPUnit.

---

## 🚀 Installation

1. Download and upload the plugin to your WordPress `wp-content/plugins/` directory.
2. Activate the plugin from the WordPress admin panel.
3. The plugin will automatically create the necessary custom post type, taxonomies, and database table.

---

## 🧪 Running Tests

This plugin uses **PHPUnit** for automated unit testing.

Follow these steps to setup the WordPress test environment and run the tests:

---

### 1. Install the WordPress test suite

Use the included script:

```bash
./bin/install-wp-tests.sh verona-test root '' 127.0.0.1
```

Where:
- `verona-test` is the database name you want to use for tests.
- `root` is your MySQL username.
- `''` (empty) is your MySQL password.
- `127.0.0.1` is your MySQL host.


This script will:
- Download WordPress Core for testing
- Setup the test libraries
- Configure the environment

---

### 2. Install PHP dependencies

If you haven't already:

```bash
composer install
```

(Assuming Rabbit Framework and PHPUnit are already included in `composer.json`.)

---

### 3. Run the tests

After setting up the test environment:

```bash
vendor/bin/phpunit
```

✅ You should see a success report showing all tests passed.

---

## 🛠️ Notes

- All tests are inside the `/tests/` directory.
- Tests are grouped by functionality:
    - `BookPostTypeTest`
    - `DatabaseMigrationTest`
    - `AdminPageTest`
    - `IsbnSavingTest`
- Tests automatically activate and deactivate the plugin before checking database migrations.

---

## 📦 Creating a Release Zip

There is a `release.sh` script included to help you create a clean ZIP file for plugin distribution.

It will:
- Create a zip without development files (like `vendor/`, `tests/`, `.git/`, `.github/`, etc.).
- Package only the necessary plugin files for production.

### Usage:

```bash
./release.sh
```

After running, you will find a clean `.zip` file (example: `book-management.zip`) inside the project root.

✅ Ready to upload to WordPress!

---

## 🧩 Requirements

- PHP 8.1 or higher
- WordPress 5.5 or higher
- Composer
- PHPUnit 9.x (recommended)

---

## 🧑‍💻 Author

**Amir Pirmoradian**  
📧 piramir77@gmail.com

---

## 📄 License

GPL-3.0-or-later  
Please see `LICENSE` file for more information.