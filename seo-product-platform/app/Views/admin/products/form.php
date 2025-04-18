<?= $this->extend('admin/layout/admin_base') ?>

<?= $this->section('admin_content') ?>

<?php
$isEdit = isset($product);
$formAction = $isEdit ? base_url('/admin/products/update/' . $product['id']) : base_url('/admin/products/create');
?>

<form action="<?= $formAction ?>" method="post" enctype="multipart/form-data"> <!-- Add enctype for file uploads -->
    <?= csrf_field() ?>

    <div class="row">
        <div class="col-md-8">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control <?= (session()->getFlashdata('errors.name')) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= old('name', $product['name'] ?? '') ?>" required>
                <?php if (session()->getFlashdata('errors.name')): ?>
                    <div class="invalid-feedback"><?= session()->getFlashdata('errors.name') ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control <?= (session()->getFlashdata('errors.description')) ? 'is-invalid' : '' ?>" id="description" name="description" rows="5"><?= old('description', $product['description'] ?? '') ?></textarea>
                 <?php if (session()->getFlashdata('errors.description')): ?>
                    <div class="invalid-feedback"><?= session()->getFlashdata('errors.description') ?></div>
                <?php endif; ?>
            </div>

             <?php if ($isEdit): ?>
                <div class="mb-3">
                    <label for="slug" class="form-label">Slug (URL)</label>
                    <input type="text" class="form-control <?= (session()->getFlashdata('errors.slug')) ? 'is-invalid' : '' ?>" id="slug" name="slug" value="<?= old('slug', $product['slug'] ?? '') ?>" required>
                     <div class="form-text">The unique URL-friendly version of the name. Lowercase letters, numbers, and hyphens only.</div>
                    <?php if (session()->getFlashdata('errors.slug')): ?>
                        <div class="invalid-feedback"><?= session()->getFlashdata('errors.slug') ?></div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                 <div class="mb-3">
                    <label class="form-label">Slug (URL)</label>
                    <input type="text" class="form-control" disabled readonly value="(Auto-generated from name)">
                     <div class="form-text">The unique URL-friendly version of the name. Will be auto-generated on creation.</div>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="meta_title" class="form-label">Meta Title (Optional)</label>
                <input type="text" class="form-control <?= (session()->getFlashdata('errors.meta_title')) ? 'is-invalid' : '' ?>" id="meta_title" name="meta_title" value="<?= old('meta_title', $product['meta_title'] ?? '') ?>">
                <div class="form-text">Optimized title for search engines (max 255 characters). If empty, product name will be used.</div>
                 <?php if (session()->getFlashdata('errors.meta_title')): ?>
                    <div class="invalid-feedback"><?= session()->getFlashdata('errors.meta_title') ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="meta_description" class="form-label">Meta Description (Optional)</label>
                <textarea class="form-control <?= (session()->getFlashdata('errors.meta_description')) ? 'is-invalid' : '' ?>" id="meta_description" name="meta_description" rows="3"><?= old('meta_description', $product['meta_description'] ?? '') ?></textarea>
                <div class="form-text">Short description for search engines (recommended 150-160 characters).</div>
                 <?php if (session()->getFlashdata('errors.meta_description')): ?>
                    <div class="invalid-feedback"><?= session()->getFlashdata('errors.meta_description') ?></div>
                <?php endif; ?>
            </div>

        </div>
        <div class="col-md-4">
             <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select <?= (session()->getFlashdata('errors.category_id')) ? 'is-invalid' : '' ?>" id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= (old('category_id', $product['category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>>
                            <?= esc($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                 <?php if (session()->getFlashdata('errors.category_id')): ?>
                    <div class="invalid-feedback"><?= session()->getFlashdata('errors.category_id') ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" class="form-control <?= (session()->getFlashdata('errors.price')) ? 'is-invalid' : '' ?>" id="price" name="price" value="<?= old('price', $product['price'] ?? '') ?>" required>
                     <?php if (session()->getFlashdata('errors.price')): ?>
                        <div class="invalid-feedback"><?= session()->getFlashdata('errors.price') ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Image Upload Placeholder -->
            <div class="mb-3">
                 <label for="image" class="form-label">Product Image <?= $isEdit ? '(Optional: Replace existing)' : '(Optional)' ?></label>
                 <input class="form-control <?= (session()->getFlashdata('errors.image')) ? 'is-invalid' : '' ?>" type="file" id="image" name="image">
                 <div class="form-text">Upload an image (e.g., JPG, PNG, WebP). Max size 1MB.</div>
                 <?php if (session()->getFlashdata('errors.image')): ?>
                    <div class="invalid-feedback"><?= session()->getFlashdata('errors.image') ?></div>
                <?php endif; ?>
                 <?php if ($isEdit && !empty($product['image_url'])): ?>
                    <div class="mt-2">
                        <?php 
                        // Construct image URL relative to base URL
                        $imageUrl = base_url(ltrim($product['image_url'], '/'));
                        ?>
                        <img src="<?= esc($imageUrl, 'attr') ?>" alt="Current Image" style="max-height: 100px; width: auto;" class="img-thumbnail">
                        <small class="d-block text-muted">Current Image</small>
                        <!-- Add option to remove image if needed -->
                        <!-- Example: <input type="checkbox" name="remove_image" value="1"> Remove Current Image -->
                    </div>
                 <?php endif; ?>
            </div>
            <!-- End Image Upload Placeholder -->

        </div>
    </div>

    <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update Product' : 'Create Product' ?></button>
    <a href="<?= base_url('/admin/products') ?>" class="btn btn-secondary">Cancel</a>
</form>

<?= $this->endSection() ?>
