<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;

class ProductController extends BaseController
{
    /**
     * Shows details for a single product.
     * We get the product 'slug' (like 'super-widget-5000') from the URL.
     * Example URL: /product/super-widget-5000
     */
    public function show($slug = null)
    {
        if (!$slug) {
            // No slug in URL? That's not right. Show 404 page.
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();
        helper('text'); // Need this for character_limiter in the view's schema section

        // Find the product in the database using its slug
        $product = $productModel->where('slug', $slug)->first();

        if (!$product) {
            // Didn't find the product? Show 404 page.
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Sorry, product not found: ' . $slug);
        }

        // We also need the category details (like its name and slug) for breadcrumbs and maybe other things.
        $category = $categoryModel->find($product['category_id']);
        if (!$category) {
             // This shouldn't really happen if the data is correct (every product should have a category).
             // But just in case, let's show a 404 page because something's missing.
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Oops! Category for this product seems to be missing.');
        }

        // Prepare the data to send to the view file
        $data = [
            'product' => $product, // The product details
            'category' => $category, // The category details
            // Set page title for browser tab & SEO. Use the product's special meta title if set, otherwise just the product name.
            'meta_title' => !empty($product['meta_title']) ? $product['meta_title'] : $product['name'],
            // Set meta description for SEO. Use the special one if set, otherwise use a shortened version of the main description.
            'meta_description' => !empty($product['meta_description']) ? $product['meta_description'] : character_limiter(strip_tags($product['description'] ?? ''), 160),
        ];

        // Load the 'product_view.php' file and pass the data to it
        return view('product_view', $data);
    }
}
