<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>

<div class="container">
    <div class="text-center my-5">
        <h1 class="display-4">Our Products</h1>
        <p class="lead">Explore products across all our categories.</p>
    </div>

    <?php if (!empty($productsByCategory)): ?>
        <?php foreach ($productsByCategory as $categoryId => $categoryData): ?>
            <div class="category-section mb-5">
                <h2 class="mb-3 border-bottom pb-2">
                    <a href="<?= base_url('category/' . $categoryData['details']['slug']) ?>" class="text-decoration-none text-dark">
                        <?= esc($categoryData['details']['name']) ?>
                    </a>
                </h2>
                
                <?php if (!empty($categoryData['products'])): ?>
                    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
                        <?php foreach ($categoryData['products'] as $product): ?>
                            <div class="col">
                                <div class="card h-100">
                                    <?php 
                                    // Construct image URL relative to base URL - Assuming image_url stores path like 'uploads/products/image.jpg'
                                    $imageUrl = !empty($product['image_url']) ? base_url(ltrim($product['image_url'], '/')) : 'https://via.placeholder.com/300x200.png?text=No+Image';
                                    ?>
                                    <img src="<?= esc($imageUrl, 'attr') ?>" class="card-img-top" alt="<?= esc($product['name'], 'attr') ?>" style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="<?= base_url('product/' . $product['slug']) ?>"><?= esc($product['name']) ?></a>
                                        </h5>
                                        <p class="card-text"><?= esc(character_limiter(strip_tags($product['description'] ?? ''), 80)) ?></p>
                                        <p class="card-text"><strong>Price: $<?= esc(number_format($product['price'], 2)) ?></strong></p>
                                    </div>
                                    <div class="card-footer">
                                         <a href="<?= base_url('product/' . $product['slug']) ?>" class="btn btn-primary btn-sm">View Details</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                     <?php if (count($categoryData['products']) > 3): // Example: Show 'View More' if more than 3 products ?>
                        <div class="text-center mt-3">
                            <a href="<?= base_url('category/' . $categoryData['details']['slug']) ?>" class="btn btn-outline-secondary">View All in <?= esc($categoryData['details']['name']) ?></a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p>No products found in this category yet.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center">No products or categories found.</p>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>
