<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h2 class="h4 mb-0 text-center">
                    <i class="bi bi-person-plus me-2"></i>Register
                </h2>
            </div>
            <div class="card-body p-4">
                <?php if (isset($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php foreach ($errors as $error): ?>
                    <p class="mb-0"><i class="bi bi-exclamation-circle me-2"></i><?=htmlspecialchars($error)?></p>
                    <?php endforeach; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?=BASE_PATH?>/register" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="bi bi-person me-1"></i>Username
                        </label>
                        <input type="text" class="form-control form-control-lg" id="username" name="username"
                            value="<?=htmlspecialchars($_POST['username'] ?? '')?>" required>
                        <div class="invalid-feedback">Please choose a username.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope me-1"></i>Email
                        </label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email"
                            value="<?=htmlspecialchars($_POST['email'] ?? '')?>" required>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="bi bi-key me-1"></i>Password
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control form-control-lg" id="password" name="password"
                                required minlength="6">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">Password must be at least 6 characters long.</div>
                        <div class="invalid-feedback">Please enter a valid password.</div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                        <i class="bi bi-person-plus me-2"></i>Register
                    </button>
                </form>
            </div>
            <div class="card-footer bg-white text-center py-3">
                <p class="mb-0">Already have an account?
                    <a href="<?=BASE_PATH?>/login" class="text-decoration-none">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const icon = this.querySelector('i');

    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
});

// Form validation
(function() {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>