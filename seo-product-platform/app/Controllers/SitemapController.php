<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class SitemapController extends BaseController
{
    public function index()
    {
        $categoryModel = new CategoryModel();
        $productModel = new ProductModel();
        helper('xml'); // Load XML helper if needed, though manual generation is fine
        helper('url');

        $baseURL = base_url();

        // Start XML output
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // 1. Add Homepage URL
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . $baseURL . "</loc>\n";
        $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n"; // Use current date or last modified date of homepage content
        $xml .= "    <changefreq>daily</changefreq>\n";
        $xml .= "    <priority>1.0</priority>\n";
        $xml .= "  </url>\n";

        // 2. Add Category URLs
        $categories = $categoryModel->select('slug, updated_at')->findAll();
        foreach ($categories as $category) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . $baseURL . 'category/' . esc($category['slug'], 'url') . "</loc>\n";
            $xml .= "    <lastmod>" . date('Y-m-d', strtotime($category['updated_at'] ?? time())) . "</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            $xml .= "  </url>\n";
        }

        // 3. Add Product URLs
        $products = $productModel->select('slug, updated_at')->findAll();
        foreach ($products as $product) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . $baseURL . 'product/' . esc($product['slug'], 'url') . "</loc>\n";
            $xml .= "    <lastmod>" . date('Y-m-d', strtotime($product['updated_at'] ?? time())) . "</lastmod>\n";
            $xml .= "    <changefreq>monthly</changefreq>\n"; // Or weekly if products change often
            $xml .= "    <priority>0.6</priority>\n";
            $xml .= "  </url>\n";
        }

        // End XML output
        $xml .= '</urlset>';

        // Set header and output
        $this->response->setHeader('Content-Type', 'application/xml');
        
        echo $xml;
        // Prevent CodeIgniter from trying to render a view
        exit(); 
    }
}
