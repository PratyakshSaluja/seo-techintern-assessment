<?php

namespace App\Controllers\Admin;

use App\Models\ProductModel;
use App\Models\CategoryModel;

class ProductsController extends BaseAdminController // Make sure it extends our admin base
{
    protected $productModel;
    protected $categoryModel;
    // Where to store uploaded product images (inside the public folder so they are web-accessible)
    protected $uploadPath = FCPATH . 'uploads/products/'; 

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        // Load helpers needed for forms, URLs, text manipulation, and file system operations
        helper(['form', 'url', 'text', 'filesystem']); 
        
        // Make sure the upload directory exists, create it if not.
        if (!is_dir($this->uploadPath)) {
            // Use 0777 for development ease, but 0775 or 0755 might be better for production servers.
            mkdir($this->uploadPath, 0777, true); 
        }
    }

    /**
     * Show the list of all products.
     */
    public function index()
    {
        // Get products, also join with categories table to get category name for display
        $products = $this->productModel->select('products.*, categories.name as category_name')
                                       ->join('categories', 'categories.id = products.category_id', 'left') // LEFT JOIN in case a category was deleted
                                       ->orderBy('products.name', 'ASC') // Sort products alphabetically
                                       ->findAll();

        $data = [
            'products' => $products,
            'meta_title' => 'Manage Products', // Title for the admin page
        ];
        return view('admin/products/index', $data);
    }

    /**
     * Show the form to add a new product.
     */
    public function new()
    {
        // We need the list of categories to populate the dropdown in the form
        $data = [
            'categories' => $this->categoryModel->orderBy('name', 'ASC')->findAll(),
            'meta_title' => 'Add New Product',
        ];
        return view('admin/products/form', $data); // Re-use the form view
    }

    /**
     * Handle the submission of the new product form.
     */
    public function create()
    {
        // Define validation rules for the submitted data
        $rules = [
            'category_id' => 'required|is_natural_no_zero|is_not_unique[categories.id]', // Must select a valid category
            'name'        => 'required|min_length[3]|max_length[150]', // Name is required
            'price'       => 'required|decimal', // Price must be a number (like 10.99)
            'description' => 'permit_empty', // Description is optional
            'meta_title'  => 'permit_empty|max_length[255]', // Optional SEO title
            'meta_description' => 'permit_empty', // Optional SEO description
            // Image validation: optional, must be uploaded, max 1MB, must be image type (jpg, png, webp etc.)
            'image' => 'permit_empty|uploaded[image]|max_size[image,1024]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]', 
        ];

        // Run validation
        if (!$this->validate($rules)) {
            // If validation fails, go back to the form with errors and old input
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // If validation passed, handle the image upload (if any)
        $imageUrl = $this->handleImageUpload(); // This returns the relative path like 'uploads/products/xyz.jpg' or null

        // Prepare data for database insertion
        $data = [
            'category_id'      => $this->request->getPost('category_id'),
            'name'             => $this->request->getPost('name'),
            // Automatically create a URL-friendly slug from the name
            'slug'             => url_title(strtolower($this->request->getPost('name')), '-', true), 
            'price'            => $this->request->getPost('price'),
            'description'      => $this->request->getPost('description'),
            'meta_title'       => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description'),
            'image_url'        => $imageUrl, // Store the relative path we got from handleImageUpload()
        ];
        
        // Make sure the generated slug is unique. If 'product-name' exists, try 'product-name-1', etc.
        $originalSlug = $data['slug'];
        $counter = 1;
        while ($this->productModel->where('slug', $data['slug'])->countAllResults() > 0) {
            $data['slug'] = $originalSlug . '-' . $counter++;
        }

        // Try to insert the data into the database
        if ($this->productModel->insert($data)) {
            // Success! Redirect to the product list with a success message.
            return redirect()->to('/admin/products')->with('message', 'Product created successfully.');
        } else {
             // If insert fails, we should delete the image we just uploaded (if any) to avoid orphaned files.
            if ($imageUrl) {
                $this->deleteImageFile(basename($imageUrl)); 
            }
            // Redirect back to the form with an error message.
            return redirect()->back()->withInput()->with('error', 'Failed to create product.');
        }
    }

    /**
     * Show the form to edit an existing product.
     * We get the product ID from the URL (e.g., /admin/products/edit/12)
     */
    public function edit($id = null)
    {
        // Find the product by its ID
        $product = $this->productModel->find($id);
        if (!$product) {
            // If no product found with this ID, show 404 page.
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Product not found');
        }

        // Prepare data for the form view
        $data = [
            'product'    => $product, // Pass the existing product data
            // Also need the list of categories for the dropdown
            'categories' => $this->categoryModel->orderBy('name', 'ASC')->findAll(),
            'meta_title' => 'Edit Product: ' . $product['name'],
        ];
        return view('admin/products/form', $data); // Re-use the same form view as 'new'
    }

    /**
     * Handle the submission of the edit product form.
     * We get the product ID from the URL.
     */
    public function update($id = null)
    {
        // Find the product first to make sure it exists
        $product = $this->productModel->find($id);
        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Product not found');
        }

        // Define validation rules for updating
        $rules = [
            'category_id' => 'required|is_natural_no_zero|is_not_unique[categories.id]',
            'name'        => 'required|min_length[3]|max_length[150]',
            // Slug must be unique, but ignore the current product's own slug
            'slug'        => 'required|max_length[150]|is_unique[products.slug,id,' . $id . ']|alpha_dash', // alpha_dash allows letters, numbers, underscore, dash
            'price'       => 'required|decimal',
            'description' => 'permit_empty',
            'meta_title'  => 'permit_empty|max_length[255]',
            'meta_description' => 'permit_empty',
             // Image validation: optional, same rules as create
            'image' => 'permit_empty|uploaded[image]|max_size[image,1024]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        // Validate the submitted data
        if (!$this->validate($rules)) {
            // If validation fails, go back to the edit form with errors and old input
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare data for database update (start with non-image fields)
        $data = [
            'category_id'      => $this->request->getPost('category_id'),
            'name'             => $this->request->getPost('name'),
            'slug'             => $this->request->getPost('slug'), // Use the slug submitted in the form (already validated for uniqueness)
            'price'            => $this->request->getPost('price'),
            'description'      => $this->request->getPost('description'),
            'meta_title'       => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description'),
        ];

        // Check if a new image was uploaded
        $newImageUrl = $this->handleImageUpload();
        if ($newImageUrl !== null) {
            // A new image was successfully uploaded.
            // Delete the old image file, if one existed.
            if (!empty($product['image_url'])) {
                 $this->deleteImageFile(basename($product['image_url']));
            }
            // Add the new image path to the data array for database update.
            $data['image_url'] = $newImageUrl;
        }
        // Note: We haven't added a checkbox to *remove* an image without replacing it. 
        // If needed, add a checkbox field like 'remove_image' and check for it here.
        // if ($this->request->getPost('remove_image') == '1' && $newImageUrl === null) {
        //     if (!empty($product['image_url'])) { $this->deleteImageFile(basename($product['image_url'])); }
        //     $data['image_url'] = null;
        // }

        // Try to update the record in the database
        if ($this->productModel->update($id, $data)) {
            // Success! Redirect to the product list with a success message.
            return redirect()->to('/admin/products')->with('message', 'Product updated successfully.');
        } else {
             // If update fails, but we uploaded a new image, we should delete that new image.
            if ($newImageUrl !== null) {
                 $this->deleteImageFile(basename($newImageUrl));
            }
            // Redirect back to the edit form with an error message.
            return redirect()->back()->withInput()->with('error', 'Failed to update product.');
        }
    }

    /**
     * Delete a product.
     * We get the product ID from the URL.
     * IMPORTANT: Using GET for deletion is bad practice for real apps (use POST/DELETE with CSRF).
     */
    public function delete($id = null)
    {
         // Find the product first
         $product = $this->productModel->find($id);
         if (!$product) {
             // Product doesn't exist, redirect back with an error.
             return redirect()->to('/admin/products')->with('error', 'Product not found.');
         }

        // Delete the associated image file from the server, if it exists.
        if (!empty($product['image_url'])) {
            $this->deleteImageFile(basename($product['image_url']));
        }
        
        // Try to delete the product record from the database
        if ($this->productModel->delete($id)) {
            // Success! Redirect to the list with a success message.
            return redirect()->to('/admin/products')->with('message', 'Product deleted successfully.');
        } else {
            // Database deletion failed. The image might still be deleted.
            // In a real app, you might want transaction rollback here.
            return redirect()->to('/admin/products')->with('error', 'Failed to delete product from database.');
        }
    }

    /**
     * Handles image upload, moves file, and returns relative path from public dir.
     * Checks if a file was uploaded via the 'image' input field.
     *
     * @param \CodeIgniter\HTTP\Files\UploadedFile|null $img (Optional, usually gets from request)
     * @return string|null Relative path like 'uploads/products/xyz.jpg' or null if no upload/failure.
     */
    private function handleImageUpload($img = null): ?string
    {
        // If no image object passed, get it from the current request
        if (!$img) {
            $img = $this->request->getFile('image');
        }

        // Check if $img is a valid uploaded file object and hasn't been moved already
        if ($img && $img->isValid() && !$img->hasMoved()) {
            // Generate a random name for the file to avoid conflicts
            $newName = $img->getRandomName();
            try {
                // Move the uploaded file to our designated upload path
                $img->move($this->uploadPath, $newName);
                // Return the path relative to the public directory (so it can be used in base_url())
                return 'uploads/products/' . $newName; 
            } catch (\Exception $e) {
                // Log the error if moving fails
                log_message('error', '[ImageUpload] Failed to move file: ' . $e->getMessage());
                return null; // Indicate failure
            }
        }
        // No valid file uploaded or it was already moved.
        return null; 
    }

    /**
     * Deletes an image file from the upload directory.
     *
     * @param string $filename The base name of the file (e.g., 'xyz.jpg')
     * @return bool True on success or if file doesn't exist, false on actual deletion failure.
     */
    private function deleteImageFile(string $filename): bool
    {
        // If filename is empty, nothing to do.
        if (empty($filename)) {
            return true; 
        }
        
        // Construct the full path to the file
        $filepath = $this->uploadPath . $filename;

        // Check if it's actually a file
        if (is_file($filepath)) {
            // Try to delete the file
            if (unlink($filepath)) {
                return true; // Successfully deleted
            } else {
                // Log error if deletion fails
                log_message('error', '[ImageDelete] Failed to delete file: ' . $filepath);
                return false; // Deletion failed
            }
        }
        // File doesn't exist, which is fine in this context (maybe already deleted).
        return true; 
    }
}
