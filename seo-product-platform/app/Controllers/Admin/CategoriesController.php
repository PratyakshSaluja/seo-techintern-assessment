<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;

class CategoriesController extends BaseAdminController // Make sure it extends our admin base
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        helper(['form', 'url', 'text']); // Load helpers needed for forms, URLs, slugs
    }

    /**
     * Show the list of all categories.
     * This is also the default page for /admin
     */
    public function index()
    {
        $data = [
            'categories' => $this->categoryModel->orderBy('name', 'ASC')->findAll(),
            'meta_title' => 'Manage Categories', // Title for the admin page
        ];
        return view('admin/categories/index', $data);
    }

    /**
     * Show the form to add a new category.
     */
    public function new()
    {
        $data = [
            'meta_title' => 'Add New Category',
        ];
        return view('admin/categories/form', $data); // Re-use the form view
    }

    /**
     * Handle the submission of the new category form.
     */
    public function create()
    {
        // Define rules for validation
        $rules = [
            'name'        => 'required|min_length[3]|max_length[100]|is_unique[categories.name]', // Name is required, must be unique
            'meta_title'  => 'permit_empty|max_length[255]',
            'meta_description' => 'permit_empty',
        ];

        // Check if the submitted data passes validation
        if (!$this->validate($rules)) {
            // If validation fails, redirect back to the form with errors and old input
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare data for database insertion
        $data = [
            'name'             => $this->request->getPost('name'),
            // Automatically create a URL-friendly slug from the name
            'slug'             => url_title(strtolower($this->request->getPost('name')), '-', true), 
            'meta_title'       => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description'),
        ];

        // Make sure the generated slug is unique. If not, add a number suffix (e.g., category-name-1)
        $originalSlug = $data['slug'];
        $counter = 1;
        while ($this->categoryModel->where('slug', $data['slug'])->countAllResults() > 0) {
            $data['slug'] = $originalSlug . '-' . $counter++;
        }

        // Try to insert the data into the database
        if ($this->categoryModel->insert($data)) {
            // Success! Redirect to the category list with a success message.
            return redirect()->to('/admin/categories')->with('message', 'Category created successfully.');
        } else {
            // Failed to insert. Redirect back to the form with an error message.
            return redirect()->back()->withInput()->with('error', 'Failed to create category.');
        }
    }

    /**
     * Show the form to edit an existing category.
     * We get the category ID from the URL (e.g., /admin/categories/edit/5)
     */
    public function edit($id = null)
    {
        // Find the category by its ID
        $category = $this->categoryModel->find($id);
        if (!$category) {
            // If no category found with this ID, show 404 page.
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Category not found');
        }

        // Prepare data for the form view
        $data = [
            'category'   => $category, // Pass the existing category data
            'meta_title' => 'Edit Category: ' . $category['name'],
        ];
        return view('admin/categories/form', $data); // Re-use the same form view as 'new'
    }

    /**
     * Handle the submission of the edit category form.
     * We get the category ID from the URL.
     */
    public function update($id = null)
    {
        // Find the category first to make sure it exists
        $category = $this->categoryModel->find($id);
        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Category not found');
        }

        // Define validation rules for updating
        $rules = [
            // Name must be unique, but ignore the current category's own name
            'name'        => 'required|min_length[3]|max_length[100]|is_unique[categories.name,id,' . $id . ']', 
            // Slug must also be unique, ignoring the current category's slug
            'slug'        => 'required|max_length[100]|is_unique[categories.slug,id,' . $id . ']|alpha_dash', // alpha_dash allows letters, numbers, underscore, dash
            'meta_title'  => 'permit_empty|max_length[255]',
            'meta_description' => 'permit_empty',
        ];

        // Validate the submitted data
        if (!$this->validate($rules)) {
            // If validation fails, go back to the edit form with errors and old input
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare data for database update
        $data = [
            'name'             => $this->request->getPost('name'),
            'slug'             => $this->request->getPost('slug'), // Use the slug submitted in the form
            'meta_title'       => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description'),
        ];

        // Try to update the record in the database
        if ($this->categoryModel->update($id, $data)) {
            // Success! Redirect to the category list with a success message.
            return redirect()->to('/admin/categories')->with('message', 'Category updated successfully.');
        } else {
            // Failed to update. Redirect back to the edit form with an error message.
            return redirect()->back()->withInput()->with('error', 'Failed to update category.');
        }
    }

    /**
     * Delete a category.
     * We get the category ID from the URL.
     * IMPORTANT: Using GET for deletion is bad practice for real apps (use POST/DELETE with CSRF).
     *            Also, consider what happens to products in this category! (Set null? Delete them? Prevent deletion?)
     */
    public function delete($id = null)
    {
        // Basic check if category exists before trying to delete
        $category = $this->categoryModel->find($id);
        if (!$category) {
             return redirect()->to('/admin/categories')->with('error', 'Category not found.');
        }
        
        // TODO: Add logic here to handle products associated with this category before deleting.
        // For now, we just delete the category.

        if ($this->categoryModel->delete($id)) {
            // Success! Redirect to the list with a success message.
            return redirect()->to('/admin/categories')->with('message', 'Category deleted successfully.');
        } else {
            // Failed to delete. Redirect with an error message.
            return redirect()->to('/admin/categories')->with('error', 'Failed to delete category.');
        }
    }
}
