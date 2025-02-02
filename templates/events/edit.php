<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header">
                <h2 class="h4 mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Event</h2>
            </div>
            <div class="card-body">
                <?php if (isset($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                    <p class="mb-0"><?=htmlspecialchars($error)?></p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php require __DIR__ . '/form.php'; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>