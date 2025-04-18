<?= $this->extend('admin/layout/admin_base') ?>

<?= $this->section('admin_content') ?>

<?php
$isEdit = isset($category);
$formAction = $isEdit ? base_url('/admin/categories/update/' . $category['id']) : base_url('/admin/categories/create');
?>

<form action="<?= $formAction ?>" method="post">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="name" class="form-label">Category Name</label>
        <input type="text" class="form-control <?= (session()->getFlashdata('errors.name')) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= old('name', $category['name'] ?? '') ?>" required>
        <?php if (session()->getFlashdata('errors.name')): ?>
            <div class="invalid-feedback">
                <?= session()->getFlashdata('errors.name') ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($isEdit): ?>
        <div class="mb-3">
            <label for="slug" class="form-label">Slug (URL)</label>
            <input type="text" class="form-control <?= (session()->getFlashdata('errors.slug')) ? 'is-invalid' : '' ?>" id="slug" name="slug" value="<?= old('slug', $category['slug'] ?? '') ?>" required>
             <div class="form-text">The unique URL-friendly version of the name. Lowercase letters, numbers, and hyphens only.</div>
            <?php if (session()->getFlashdata('errors.slug')): ?>
                <div class="invalid-feedback">
                    <?= session()->getFlashdata('errors.slug') ?>
                </div>
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
        <input type="text" class="form-control <?= (session()->getFlashdata('errors.meta_title')) ? 'is-invalid' : '' ?>" id="meta_title" name="meta_title" value="<?= old('meta_title', $category['meta_title'] ?? '') ?>">
        <div class="form-text">Optimized title for search engines (max 255 characters). If empty, category name will be used.</div>
         <?php if (session()->getFlashdata('errors.meta_title')): ?>
            <div class="invalid-feedback">
                <?= session()->getFlashdata('errors.meta_title') ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="meta_description" class="form-label">Meta Description (Optional)</label>
        <textarea class="form-control <?= (session()->getFlashdata('errors.meta_description')) ? 'is-invalid' : '' ?>" id="meta_description" name="meta_description" rows="3"><?= old('meta_description', $category['meta_description'] ?? '') ?></textarea>
        <div class="form-text">Short description for search engines (recommended 150-160 characters).</div>
         <?php if (session()->getFlashdata('errors.meta_description')): ?>
            <div class="invalid-feedback">
                <?= session()->getFlashdata('errors.meta_description') ?>
            </div>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update Category' : 'Create Category' ?></button>
    <a href="<?= base_url('/admin/categories') ?>" class="btn btn-secondary">Cancel</a>
</form>

<?= $this->endSection() ?>
