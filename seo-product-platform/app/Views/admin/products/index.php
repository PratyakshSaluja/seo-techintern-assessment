<?= $this->extend('admin/layout/admin_base') ?>

<?= $this->section('admin_content') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <!-- <h1 class="h2">Manage Products</h1> -->
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('/admin/products/new') ?>" class="btn btn-sm btn-outline-primary">
            Add New Product
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Category</th>
                <th scope="col">Slug</th>
                <th scope="col">Price</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= esc($product['id']) ?></td>
                        <td><?= esc($product['name']) ?></td>
                        <td><?= esc($product['category_name'] ?? 'N/A') ?></td> <!-- Display category name from join -->
                        <td><?= esc($product['slug']) ?></td>
                        <td>$<?= esc(number_format($product['price'], 2)) ?></td>
                        <td>
                            <a href="<?= base_url('/admin/products/edit/' . $product['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <a href="<?= base_url('/admin/products/delete/' . $product['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No products found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
