# Agribusiness — Agribusiness Web Application

A PHP 8 + MySQL/MariaDB student project for connecting smallholder farmers with buyers. Built to run locally on XAMPP.

## Features
- Public Home, About, Produce Catalogue, Product Detail and Contact pages.
- Registration/login for Farmer and Buyer; administrator role for approval and order management.
- Farmers create, edit and delete listings. New/edited listings require approval.
- Buyers search/filter approved produce, add to cart, submit orders for administrator approval, and print/save receipts as PDF.
- Admin can approve or reject each buyer order. Stock is deducted only when an order is approved; approved orders then move through placed → confirmed → dispatched → delivered.
- PDO prepared statements, password hashing, server-side validation, CSRF tokens, role checks, responsive layout.

## Requirements
XAMPP with PHP 8+ and MySQL/MariaDB. Enable `pdo_mysql` in PHP if disabled.

## Run on XAMPP (Windows)
1. Copy the `Agribusiness` application folder into `C:\xampp\htdocs\` (or copy the contents of this folder into `htdocs\Agribusiness`).
2. Start Apache and MySQL in the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`.
4. Choose **Import**, select `database/agrihub.sql`, then click **Import/Go**. The script creates the `Agribusiness` database and tables. If you already have the database, import `database/upgrade_order_approval.sql` instead to update the existing orders table.
5. Check `config.php`: default local XAMPP values are database `Agribusiness`, user `root`, empty password. If your MySQL root account has a password, update the local config.
6. Create an administrator account from Command Prompt: `cd C:\xampp\htdocs\Agrihub` then `C:\xampp\php\php.exe scripts\create_admin.php admin@example.com "ReplaceWithStrongPassword123!"`. Use the email/password you supplied to log in. Keep this script out of the public web root on a live server.
7. Visit `http://localhost/Agribusiness/`.
8. Register a farmer and a buyer. Log in as admin and approve the farmer's listing. Log in as buyer to search and submit an order. The order stays pending until the admin approves or rejects it.

## Important limitations / security notes
- This is an assessment prototype, not a production marketplace. It does not process payments, verify seller identity, send email, or implement image uploads. Listing photos use an optional image URL.
- Change the sample contact email before publishing. Do not commit real credentials to GitHub.
- For public deployment, use a dedicated database user with least privilege, strong secrets, HTTPS, backups, error logging and current PHP. Do not expose database error details.
- Admin account creation is CLI-only; do not run it through a public browser.

## Data model
`users`, `categories`, `produce`, `orders`, `order_items`. Relationships are documented in `docs/ERD.md`.

## Git
Use `git init`, add a `.gitignore`, commit the source and push to a private/public repository as required by your instructor. GitHub is for source control; GitHub Pages does not execute PHP. See `Practical_Assessment_3/DEPLOYMENT_GUIDE.md` for a PHP-compatible hosting route.
