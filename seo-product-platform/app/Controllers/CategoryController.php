<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class CategoryController extends BaseController
{
    /**
     * Shows products belonging to a specific category.
     * We get the category 'slug' (like 'electronics') from the URL.
     * Example URL: /category/electronics
     */
    public function show($slug = null)
    {
        if (!$slug) {
            // If no slug is given in the URL, something's wrong. Show 404 page.
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $categoryModel = new CategoryModel();
        $productModel = new ProductModel();
        helper('text'); // Need this for character_limiter in the view

        // Find the category in the database using the slug from the URL
        $category = $categoryModel->where('slug', $slug)->first();

        if (!$category) {
            // If we didn't find a category with that slug, show 404 page.
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Sorry, category not found: ' . $slug);
        }

        // Now, find all products that belong to this category (using its ID)
        $products = $productModel->where('category_id', $category['id'])
                                 ->orderBy('name', 'ASC') // List products alphabetically, looks neat
                                 ->findAll();

        // Prepare the data we need to send to the view file
        $data = [
            'category' => $category, // The category details itself
            'products' => $products, // The list of products found
            // Set the page title for browser tab & SEO. Use the special meta title if the admin entered one, otherwise just use the category name.
            'meta_title' => !empty($category['meta_title']) ? $category['meta_title'] : $category['name'],
            // Set the meta description for SEO. Use the special one if entered, otherwise make a simple default one.
            'meta_description' => !empty($category['meta_description']) ? $category['meta_description'] : 'Check out products in the ' . $category['name'] . ' category.',
        ];

        // Load the 'category_view.php' file and pass all this data to it
        return view('category_view', $data);
    }
}
