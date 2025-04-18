<?= $this->extend('admin/layout/admin_base') ?>

<?= $this->section('admin_content') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <!-- <h1 class="h2">Manage Categories</h1> -->
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('/admin/categories/new') ?>" class="btn btn-sm btn-outline-primary">
            Add New Category
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Slug</th>
                <th scope="col">Meta Title</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= esc($category['id']) ?></td>
                        <td><?= esc($category['name']) ?></td>
                        <td><?= esc($category['slug']) ?></td>
                        <td><?= esc($category['meta_title']) ?></td>
                        <td>
                            <a href="<?= base_url('/admin/categories/edit/' . $category['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <a href="<?= base_url('/admin/categories/delete/' . $category['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No categories found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
