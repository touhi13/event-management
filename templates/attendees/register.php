<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header">
                <h2 class="h4 mb-0"><i class="bi bi-person-plus me-2"></i>Register for
                    <?=htmlspecialchars($event['name'])?></h2>
            </div>
            <div class="card-body">
                <?php if (isset($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                    <p class="mb-0"><?=htmlspecialchars($error)?></p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="<?=htmlspecialchars($_POST['name'] ?? '')?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?=htmlspecialchars($_POST['email'] ?? '')?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                            value="<?=htmlspecialchars($_POST['phone'] ?? '')?>" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Register
                        </button>
                        <a href="/events" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Events
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>