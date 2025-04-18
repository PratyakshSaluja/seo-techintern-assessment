<?= $this->extend('layout/base') ?>

<?= $this->section('schema') ?>
<!-- Add BreadcrumbList Schema for Category Page -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [{
    "@type": "ListItem",
    "position": 1,
    "name": "Home",
    "item": "<?= base_url('/') ?>"
  },{
    "@type": "ListItem",
    "position": 2,
    "name": "<?= esc($category['name']) ?>"
  }]
}
</script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page"><?= esc($category['name']) ?></li>
  </ol>
</nav>

<h1><?= esc($category['name']) ?></h1>
<hr>

<?php if (!empty($products)): ?>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($products as $product): ?>
            <div class="col">
                <div class="card h-100">
                    <?php 
                    // Construct image URL relative to base URL
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
<?php else: ?>
    <p>No products found in this category.</p>
<?php endif; ?>

<?= $this->endSection() ?>
