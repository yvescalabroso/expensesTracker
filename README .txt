============================================================
  EXPENSE TRACKER — README
============================================================

A personal finance web app for tracking monthly expenses
and income, built with PHP, PostgreSQL, and vanilla JS.
No login required — single-user, session-free design.

------------------------------------------------------------
  FEATURES
------------------------------------------------------------

  - Add expense or income transactions from a single form
  - Toggle between Expense and Income transaction types
  - Monthly navigation to browse past and future months
  - Dashboard summary cards: Income, Spent, Net Balance
  - Spending breakdown by category with visual progress bars
  - Filter transaction list by category
  - Delete individual transactions with confirmation prompt
  - Alert messages for success and validation errors
  - Responsive layout (mobile-friendly)

------------------------------------------------------------
  TECH STACK
------------------------------------------------------------

  Backend  : PHP (procedural, no framework)
  Database : PostgreSQL (schema: expenses)
  Frontend : Vanilla JavaScript, HTML5, CSS3
  Fonts    : Syne, DM Sans (Google Fonts)

------------------------------------------------------------
  FILE STRUCTURE
------------------------------------------------------------

  index.php      — Main HTML page, categories config, bootstraps all files
  actions.php    — Handles POST form submissions (add/delete transactions)
  data.php       — Loads and filters transaction data for the current view
  functions.php  — DB queries, utility/helper functions
  dbcon.php      — PDO database connection
  style.css      — Styles and layout (dark theme)

------------------------------------------------------------
  EXPENSE CATEGORIES
------------------------------------------------------------

  Food, Transport, Housing, Health, Entertainment,
  Shopping, Education, Sports, Other

  Income transactions are automatically assigned the
  'Income' category and do not require a category selection.

------------------------------------------------------------
  REQUIREMENTS
------------------------------------------------------------

  - PHP 8.0 or higher
  - PostgreSQL 13 or higher
  - A web server (Apache, Nginx, or PHP built-in server)
  - PDO and pdo_pgsql PHP extensions enabled

------------------------------------------------------------
  DATABASE SETUP
------------------------------------------------------------

  1. Create the database and schema:

       CREATE DATABASE expenses_db;

       \c expenses_db

       CREATE SCHEMA expenses;

  2. Create the transactions table:

       CREATE TABLE expenses.transactions (
         transaction_id SERIAL PRIMARY KEY,
         description    TEXT NOT NULL,
         amount         NUMERIC(12,2) NOT NULL CHECK (amount > 0),
         category       TEXT NOT NULL,
         is_income      BOOLEAN NOT NULL DEFAULT FALSE,
         date           DATE NOT NULL DEFAULT CURRENT_DATE,
         created_at     TIMESTAMPTZ NOT NULL DEFAULT NOW()
       );

------------------------------------------------------------
  CONFIGURATION
------------------------------------------------------------

  Edit dbcon.php to match your PostgreSQL credentials:

       $username = "postgres";
       $password = "your_password_here";

  The connection string targets:

       Host     : localhost
       Port     : 5432
       Database : expenses_db
       Schema   : expenses

------------------------------------------------------------
  RUNNING LOCALLY
------------------------------------------------------------

  Option A — PHP built-in server:

       php -S localhost:8000

  Then open http://localhost:8000 in your browser.

  Option B — Apache/Nginx:

  Place all files in your web root (e.g. /var/www/html or
  htdocs) and access via your configured virtual host.

------------------------------------------------------------
  USAGE
------------------------------------------------------------

  1. Open the app in a browser.
  2. Use the month arrows (< >) to navigate between months.
  3. Fill in the "Add Transaction" form:
       - Toggle between "Expense" and "Income" type
       - Enter a description, amount, date
       - Pick a category (expenses only; auto-set for income)
       - Click the submit button to save
  4. Use the filter tabs above the transaction list to view
     entries by category.
  5. Click the X button on any row to delete a transaction.

------------------------------------------------------------
  NOTES
------------------------------------------------------------

  - All amounts are displayed in Philippine Peso (PHP).
  - This is a single-user app with no authentication.
    It is intended for personal/local use only.
  - Transaction list is sorted by date descending,
    then by ID descending for same-day entries.
  - Category filter tabs only appear for categories that
    have at least one transaction in the current month.

============================================================
