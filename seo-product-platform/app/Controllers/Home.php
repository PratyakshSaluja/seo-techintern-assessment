<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class Home extends BaseController
{
    public function index(): string
    {
        helper('text'); // Need this for cutting down long descriptions (character_limiter)
        
        $categoryModel = new CategoryModel();
        $productModel = new ProductModel();

        // Get all categories, sorted nicely by name
        $categories = $categoryModel->orderBy('name', 'ASC')->findAll();
        $productsByCategory = [];

        // Loop through each category and grab its products
        foreach ($categories as $category) {
            // Find products belonging to this category, also sorted by name
            $products = $productModel->where('category_id', $category['id'])
                                     ->orderBy('name', 'ASC')
                                     // ->limit(6) // Uncomment this if you want to show only a few products per category on the homepage
                                     ->findAll();
            
            // Only add the category to our list if it actually has products
            if (!empty($products)) {
                 $productsByCategory[$category['id']] = [
                    'details' => $category, // Category info (name, slug etc.)
                    'products' => $products // List of products in this category
                 ];
            }
        }

        // Data to send to the homepage view
        $data = [
            'productsByCategory' => $productsByCategory,
            'meta_title' => 'Welcome to Our Product Platform', // Title for the browser tab & SEO
            'meta_description' => 'Browse our wide range of products across various categories.', // Short description for SEO
        ];

        // Load the view file 'welcome_message.php' and pass the data
        return view('welcome_message', $data);
    }
}
