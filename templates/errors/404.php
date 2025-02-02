<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <h1 class="display-1 text-muted">404</h1>
                    <h2 class="h4 mb-4">Page Not Found</h2>
                    <p class="text-muted mb-4">The page you are looking for might have been removed, had its name
                        changed, or is temporarily unavailable.</p>
                    <a href="<?=BASE_PATH?>/" class="btn btn-primary">
                        <i class="bi bi-house-door me-2"></i>Go Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>