# Simple Product Website (CodeIgniter 4) 

This is a basic website I put together using CodeIgniter 4 (a PHP framework). The main idea was to build a product listing site that's easy for search engines like Google to understand and rank well (that's the SEO part).

It's got a simple admin section where you can add product categories (like 'Electronics', 'Clothing') and then add products under those categories. These products then show up on the main website.

We've focused on making sure the URLs are clean (like `/product/my-cool-gadget` instead of `/product.php?id=123`), and that important info like page titles and descriptions are set up properly for search engines.

## What's Inside? (Features)

*   **Main Website (What visitors see):**
    *   **Homepage (`/`):** Shows a list of products, nicely grouped by their category.
    *   **Category Pages (`/category/your-category-name`):** Shows all products listed under a specific category.
    *   **Product Pages (`/product/your-product-name`):** Shows the details for a single product (name, description, price, image).
    *   **Clean URLs:** Uses easy-to-read names in the web address.
    *   **SEO Friendly:** Automatically sets good page titles and descriptions. You can also set custom ones in the admin panel.
    *   **Schema Markup:** Includes special hidden code (JSON-LD for Breadcrumbs & Products) that helps Google understand the page content better (like knowing it's a product page with a specific price).
    *   **Mobile Friendly:** Uses Bootstrap 5, so it should look decent on phones and tablets too.
*   **Admin Panel (`/admin`):**
    *   **Manage Categories:** Add new categories, edit existing ones, or delete them.
    *   **Manage Products:** Add new products (with name, price, description, image, category), edit, or delete them.
    *   **Auto URLs (Slugs):** When you add a category or product, it automatically creates the clean URL part from the name.
    *   **Edit URLs (Slugs):** You can change the URL part later if you need to (just make sure it stays unique!).
    *   **Custom SEO Fields:** You can write your own specific 'Meta Title' and 'Meta Description' for any category or product if you don't like the automatic ones.
    *   **Image Upload:** Lets you upload a picture for each product.
*   **Other SEO Bits:**
    *   **Sitemap (`/sitemap.xml`):** Automatically generates a list of all your important pages (homepage, categories, products) for search engines to find easily. **Important:** It uses the `app.baseURL` from your `.env` file, so make sure this is set to your live domain before deploying!
    *   **Robots.txt (`/robots.txt`):** A simple file that tells search engine bots which parts of the site they shouldn't try to index (like the admin section or system folders).

## Tech Used

*   **PHP:** Version 8.0 or higher.
*   **Framework:** CodeIgniter 4
*   **Database:** MySQL set up the connection details in the `.env` file.
*   **Frontend:** Bootstrap 5 (For styling and layout), basic HTML/CSS.
*   **Dependencies:** Managed using Composer (a PHP package manager).

## Setup Guide

1.  **Download the Code:**
    *   Get the project files (clone the repository or download the ZIP).
    *   Open your command line/terminal, navigate into the project folder:
        ```bash
        cd path/to/where/you/put/seo-product-platform 
        ```

2.  **Install Dependencies:**
    *   You'll need [Composer](https://getcomposer.org/) installed on your system.
    *   In the terminal (inside the project folder), run:
        ```bash
        composer install
        ```
    *   *(Troubleshooting: If you see errors about missing PHP things like `intl` or `zip`, you might need to edit your `php.ini` file (find it in your XAMPP/WAMP control panel) and uncomment or enable those extensions. Remember to restart your Apache server after changing `php.ini`.)*

3.  **Environment Setup (.env file):**
    *   Look for the file named `env` (it has no file extension). Make a copy of it in the same folder and name the copy `.env`.
    *   Open `.env` with a text editor.
    *   **Crucial Step:** Update the database section to match your local MySQL setup:
        ```dotenv
        # Change this if your local setup uses a different address or port
        app.baseURL = 'http://localhost:8080/' 

        # Your MySQL details (check your XAMPP/WAMP/MAMP settings)
        database.default.hostname = localhost
        database.default.database = seo_product_platform # You can name your database anything, just be consistent
        database.default.username = root # Often 'root' for local setups
        database.default.password =  # Often blank password for local setups, enter yours if you set one
        database.default.DBDriver = MySQLi
        database.default.port = 3306 
        ```
    *   Make sure `app.baseURL` matches the address you'll use to access the site locally.

4.  **Create the Database:**
    *   Make sure your MySQL server (e.g., via XAMPP control panel) is running.
    *   Using a tool like phpMyAdmin (usually accessible via `http://localhost/phpmyadmin`), create a new, empty database with the exact name you put in the `.env` file (e.g., `seo_product_platform`).

5.  **Set Up Database Tables (Migrations):**
    *   Go back to your terminal (still in the project folder).
    *   Run this CodeIgniter command to create the `categories` and `products` tables:
        ```bash
        php spark migrate
        ```

6.  **Folder Permissions (Maybe):**
    *   The `writable` folder needs to be, well, writable by the web server process. On Windows with XAMPP/WAMP, this is usually fine. On Linux/Mac, you might need to adjust permissions if you get errors about sessions or logs (e.g., `chmod -R 777 writable`). *Be careful with 777 permissions on a live server, though!*

7.  **Start the Server!**
    *   In the terminal, run:
        ```bash
        php spark serve
        ```
    *   It should say something like "CodeIgniter development server started on http://localhost:8080".
    *   Open that address (`http://localhost:8080` or whatever your `app.baseURL` is) in your web browser!

    *   **Homepage:** `http://localhost:8080/`
    *   **Admin Panel:** `http://localhost:8080/admin` (redirects to categories)
    *   **Categories Admin:** `http://localhost:8080/admin/categories`
    *   **Products Admin:** `http://localhost:8080/admin/products`
    *   **Sitemap:** `http://localhost:8080/sitemap.xml`

    Go add some categories and products in the admin panel first, then check out the main site!

## How the SEO Stuff Works (Quick Overview)

*   **Clean URLs:** We use "slugs" (like `my-cool-product`) in the URLs. These are generated automatically from names when you add stuff in the admin panel. The routes are set up in `app/Config/Routes.php`.
*   **Page Titles & Descriptions:** The controllers (`CategoryController.php`, `ProductController.php`) grab the data. They check if you entered a custom meta title/description in the admin panel. If yes, they use that. If not, they use the product/category name for the title and make a basic description. This info is passed to the main layout file (`app/Views/layout/base.php`) which puts it in the `<head>` section.
*   **Schema Code (JSON-LD):** We added `<script type="application/ld+json">` blocks directly in the view files (`category_view.php`, `product_view.php`). This gives search engines structured info about breadcrumbs (page hierarchy) and product details (name, price, image, etc.).
*   **Sitemap (`sitemap.xml`):** The `SitemapController.php` fetches all categories and products from the database and generates the XML file on the fly when you visit `/sitemap.xml`. **Important:** It uses the `app.baseURL` from your `.env` file, so make sure this is set to your live domain before deploying!
*   **Robots.txt:** Just a simple text file in the `public` folder telling bots not to crawl `/admin`, `/app`, `/system`, `/writable` and pointing them to the sitemap.

## Deployment Link

[http://your-live-website-link.com](http://your-live-website-link.com)
