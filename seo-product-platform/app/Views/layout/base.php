<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- SEO Meta Tags -->
    <title><?= esc($meta_title ?? 'Product Platform') ?></title>
    <meta name="description" content="<?= esc($meta_description ?? 'Welcome to our Product Platform') ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Add any custom CSS here -->
    
    <!-- Schema Markup (will be added dynamically in specific views) -->
    <?= $this->renderSection('schema') ?>

</head>
<body>
    <?php
        // Fetch categories for navbar - simple approach
        // In a larger app, consider passing this from BaseController or using view composers/cells
        try {
            $categoryModel = new \App\Models\CategoryModel();
            $navCategories = $categoryModel->orderBy('name', 'ASC')->findAll();
        } catch (\Throwable $e) {
            // Handle potential database errors gracefully in production
             log_message('error', 'Error fetching categories for navbar: ' . $e->getMessage());
            $navCategories = []; // Ensure $navCategories is an array
        }
        helper('url'); // Ensure URL helper is loaded
        helper('uri'); // Load URI helper for active link check
    ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/') ?>">Product Platform</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= (uri_string() == '/') ? 'active' : '' ?>" aria-current="page" href="<?= base_url('/') ?>">Home</a>
                    </li>
                    <?php if (!empty($navCategories)): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= (strpos(uri_string(), 'category/') === 0) ? 'active' : '' ?>" href="#" id="navbarDropdownCategories" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Categories
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownCategories">
                                <?php foreach ($navCategories as $navCat): ?>
                                    <li><a class="dropdown-item" href="<?= base_url('category/' . $navCat['slug']) ?>"><?= esc($navCat['name']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto">
                     <li class="nav-item">
                        <a class="nav-link <?= (strpos(uri_string(), 'admin') === 0) ? 'active' : '' ?>" href="<?= base_url('admin/categories') ?>">Admin Panel</a> <!-- Link to admin categories -->
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="mt-5 py-3 bg-light text-center">
        <div class="container">
            <span class="text-muted">© <?= date('Y') ?> Product Platform</span>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <!-- Add any custom JS here -->
</body>
</html>
