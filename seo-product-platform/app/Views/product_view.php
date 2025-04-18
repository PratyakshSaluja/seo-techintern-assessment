<?= $this->extend('layout/base') ?>

<?= $this->section('schema') ?>
<!-- Add Product and BreadcrumbList Schema for Product Page -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "<?= esc($product['name']) ?>",
  <?php // Use absolute URL for schema image ?>
  "image": "<?= !empty($product['image_url']) ? base_url(ltrim($product['image_url'], '/')) : '' ?>", 
  "description": "<?= esc($product['meta_description'] ?? character_limiter(strip_tags($product['description'] ?? ''), 160)) ?>",
  "sku": "<?= esc($product['id']) ?>", // Use product ID as SKU or add a dedicated SKU field
  "offers": {
    "@type": "Offer",
    "url": "<?= current_url() ?>",
    "priceCurrency": "USD", // Adjust currency code if needed
    "price": "<?= esc($product['price']) ?>",
    "availability": "https://schema.org/InStock", // Adjust based on actual stock status
    "itemCondition": "https://schema.org/NewCondition" // Adjust if selling used items
  }
  // Optional: Add brand, reviews, aggregateRating etc. if applicable
}
</script>
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
    "name": "<?= esc($category['name']) ?>",
    "item": "<?= base_url('category/' . $category['slug']) ?>"
  },{
    "@type": "ListItem",
    "position": 3,
    "name": "<?= esc($product['name']) ?>"
  }]
}
</script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('category/' . $category['slug']) ?>"><?= esc($category['name']) ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?= esc($product['name']) ?></li>
  </ol>
</nav>

<div class="row">
    <div class="col-md-6">
        <?php 
        // Construct image URL relative to base URL
        $imageUrl = !empty($product['image_url']) ? base_url(ltrim($product['image_url'], '/')) : 'https://via.placeholder.com/600x400.png?text=No+Image';
        ?>
        <img src="<?= esc($imageUrl, 'attr') ?>" class="img-fluid rounded mb-3" alt="<?= esc($product['name'], 'attr') ?>">
    </div>
    <div class="col-md-6">
        <h1><?= esc($product['name']) ?></h1>
        <hr>
        <p class="lead"><strong>Price: $<?= esc(number_format($product['price'], 2)) ?></strong></p>
        <div class="product-description mb-4">
            <?= $product['description'] // Output description HTML as is, assuming it's trusted or sanitized on input ?>
        </div>
        <button class="btn btn-primary btn-lg">Add to Cart</button> <!-- Placeholder button -->
    </div>
</div>

<?= $this->endSection() ?>
